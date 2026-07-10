<?php

declare (strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\SetupSteps\SetupStepCompletions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;

/**
 * @property WPSetupStepCompletion[] $elements;
 * @method WPSetupStepCompletion getByUniqueKey(string $uniqueKey)
 * @method WPSetupStepCompletion[] getElements
 * @method WPSetupStepCompletion first
 */
//#[LazyLoadRepo(LazyLoadRepo::INTERNAL_DB, InternalDBWPSetupStepCompletions::class)]
class WPSetupStepCompletions extends EntitySet
{

}