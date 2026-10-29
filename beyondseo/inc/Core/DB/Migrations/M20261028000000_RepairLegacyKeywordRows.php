<?php
declare(strict_types=1);

namespace RankingCoach\Inc\Core\DB\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

use RankingCoach\Inc\Core\DB\AbstractMigration;
use RankingCoach\Inc\Core\DB\DatabaseTablesManager;

/**
 * 1.2.x stored keywords in the `content` column and left `template` empty. DropRedundantMetaTagsColumns
 * dropped `content` and MigrateLegacyMetaToMetaTagsTable skipped posts that already had a keywords row,
 * so those rows kept an empty template, which the strict keyword decoder rejects (HTTP 500 on save).
 * Rebuild them from the legacy postmeta, which 1.2.x wrote alongside.
 */
class M20261028000000_RepairLegacyKeywordRows extends AbstractMigration
{
    public function up(): bool
    {
        if (!$this->dbManager->tableExists(DatabaseTablesManager::DATABASE_MOD_METATAGS)) {
            return true;
        }

        $wpdb  = $this->dbManager->db()->db;
        $table = $this->getTableName(DatabaseTablesManager::DATABASE_MOD_METATAGS);
        $where = "type = 'keywords' AND TRIM(template) = ''";

        $postIds = $wpdb->get_col("SELECT post_id FROM `$table` WHERE $where");
        if (empty($postIds)) {
            return true;
        }

        if ($wpdb->query("DELETE FROM `$table` WHERE $where") === false) {
            $this->log("Could not delete empty keyword rows from $table: " . $wpdb->last_error, 'ERROR');
            return false;
        }

        $legacy = new M20260707000000_MigrateLegacyMetaToMetaTagsTable();
        foreach ($postIds as $postId) {
            $legacy->migrateKeywords((int) $postId);
        }

        return true;
    }

    public function down(): bool
    {
        return true;
    }

    public function getDescription(): string
    {
        return 'Rebuild keyword rows left with an empty template by the 1.2.x upgrade from legacy postmeta';
    }
}
