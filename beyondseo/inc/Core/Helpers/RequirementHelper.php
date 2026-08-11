<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

use RankingCoach\Inc\Core\DB\DatabaseManager;
use RankingCoach\Inc\Core\DB\DatabaseTablesManager;

class RequirementHelper
{
    /**
     * Map of requirement names to their entity aliases.
     */
    public const ENTITY_ALIAS_MAP = [
        'businessEmailAddress' => 'emailaddress',
        'businessWebsiteUrl'   => 'websiteurl',
        'businessName'         => 'name',
        'businessDescription'  => 'description',
        'businessAddress'      => 'address',
        'businessServiceArea'  => 'servicearea',
        'businessKeywords'     => 'keywords',
        'businessCategories'   => 'categories',
        'businessGeoAddress'   => 'geoaddress',
    ];

    /**
     * Update multiple requirements at once.
     * 
     * @param array $requirements Associative array of requirement name => value
     */
    public static function updateRequirements(array $requirements): void
    {
        foreach ($requirements as $name => $val) {
            self::updateRequirement($name, $val);
        }
    }

    /**
     * Update a single requirement in the database.
     *
     * @param string $name Requirement name
     * @param mixed $val Requirement value
     * @throws \JsonException
     */
    public static function updateRequirement(string $name, $val): void
    {
        if (!isset(self::ENTITY_ALIAS_MAP[$name])) {
            return;
        }

        $alias = self::ENTITY_ALIAS_MAP[$name];

        // Convert object to array if needed
        if (is_object($val)) {
            $val = json_decode(json_encode($val, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        }

        // Handle category name to ID mapping
        if ($name === 'businessCategories' && is_array($val)) {
            $isListOfIds = !empty($val) && array_reduce($val, fn($carry, $item) => $carry && is_int($item), true);
            
            if (!$isListOfIds) {
                $locale = WordpressHelpers::current_language_code_helper();
                $translatedByName = \beyondseo_get_translated_categories($locale, 'name');
                if (!empty($translatedByName)) {
                    $ids = [];
                    foreach ($val as $catName) {
                        $formattedName = ucfirst(strtolower((string)$catName));
                        if (isset($translatedByName[$formattedName])) {
                            $ids[] = (int) $translatedByName[$formattedName]['id'];
                        }
                    }
                    $val = array_values($ids);
                }
            }
        }

        // Format value for database
        if (is_array($val) || is_object($val)) {
            $dbValue = json_encode($val, JSON_THROW_ON_ERROR);
        } elseif (is_bool($val)) {
            $dbValue = $val ? 'y' : 'n';
        } else {
            $dbValue = sanitize_text_field((string)$val);
        }

        // Hardcoded rule for service area from legacy logic
        if ($name === 'businessServiceArea') {
            $dbValue = 'y';
        }

        if ($dbValue === '') {
            return;
        }

        $db = DatabaseManager::getInstance();
        $existing = $db->table(DatabaseTablesManager::DATABASE_SETUP)
            ->select(['id'])
            ->where('setupRequirement', $name)
            ->first();

        if ($existing) {
            $db->update(
                DatabaseTablesManager::DATABASE_SETUP,
                ['value' => $dbValue, 'entityAlias' => $alias],
                ['setupRequirement' => $name]
            );
        } else {
            $db->insert(
                DatabaseTablesManager::DATABASE_SETUP,
                [
                    'setupRequirement' => $name,
                    'entityAlias'      => $alias,
                    'value'            => $dbValue,
                ]
            );
        }

        // Refresh steps completion status
        self::refreshStepsCompletion();
    }

    /**
     * Refresh the completion status of all setup steps based on current requirements.
     */
    public static function refreshStepsCompletion(): void
    {
        $db = DatabaseManager::getInstance();
        
        // 1. Get all requirements and their values
        $requirementsRows = $db->table(DatabaseTablesManager::DATABASE_SETUP)
            ->select(['setupRequirement', 'value'])
            ->get();

        $satisfiedRequirements = [];
        if (is_array($requirementsRows)) {
            foreach ($requirementsRows as $reqRow) {
                $reqRow = (array) $reqRow;
                $reqName = $reqRow['setupRequirement'] ?? '';
                $reqValue = $reqRow['value'] ?? null;
                
                if ($reqValue !== null && $reqValue !== '') {
                    $decoded = json_decode((string)$reqValue, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (!empty($decoded)) {
                            $satisfiedRequirements[$reqName] = $decoded;
                        }
                    } else {
                        $satisfiedRequirements[$reqName] = $reqValue;
                    }
                }
            }
        }

        // 2. Get all steps
        $dbSteps = $db->table(DatabaseTablesManager::DATABASE_SETUP_STEPS)
            ->select(['id', 'requirements', 'completed'])
            ->get();

        if (is_array($dbSteps)) {
            foreach ($dbSteps as $stepRow) {
                $stepRow = (array) $stepRow;
                $stepId = (int) $stepRow['id'];
                $requirementsStr = $stepRow['requirements'] ?? '';
                $stepRequirements = array_filter(array_map('trim', explode(',', $requirementsStr)));

                if (empty($stepRequirements)) {
                    continue;
                }

                $allSatisfied = true;
                foreach ($stepRequirements as $req) {
                    if (!isset($satisfiedRequirements[$req])) {
                        $allSatisfied = false;
                        break;
                    }
                }

                if ($allSatisfied && (int)($stepRow['completed'] ?? 0) === 0) {
                    $db->update(
                        DatabaseTablesManager::DATABASE_SETUP_STEPS,
                        ['completed' => 1],
                        ['id' => $stepId]
                    );
                }
            }
        }
    }
}
