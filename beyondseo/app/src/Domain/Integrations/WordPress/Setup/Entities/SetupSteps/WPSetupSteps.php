<?php

declare (strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\SetupSteps;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Entities\BaseObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptions;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptionsTrait;

/**
 * @property WPSetupStep[] $elements;
 * @method WPSetupStep getByUniqueKey(string $uniqueKey)
 * @method WPSetupStep[] getElements
 * @method WPSetupStep first
 */
#[QueryOptions]
//#[LazyLoadRepo(repoType:LazyLoadRepo::INTERNAL_DB, repoClass:InternalDBWPSetupSteps::class)]
class WPSetupSteps extends EntitySet
{
    use QueryOptionsTrait;

    /** @var WPSetupStep[]  */
    private array $setupStepsByNumber = [];

    /**
     * Add elements to the collection
     *
     * @param BaseObject ...$elements
     */
    public function add(?BaseObject &...$elements): void
    {
        foreach ($elements as $element){
            /** @var WPSetupStep $element */
            $this->setupStepsByNumber[$element->stepNumber] = $element;
        }
        parent::add(...$elements);
    }

    /**
     * Get SetupStep by number
     *
     * @param int $stepNumber
     * @return WPSetupStep|null
     */
    public function getSetupStepByNumber(int $stepNumber) :?WPSetupStep {
        return $this->setupStepsByNumber[$stepNumber] ?? null;
    }
}