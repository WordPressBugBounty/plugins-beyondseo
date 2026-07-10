<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\SetupSteps\SetupStepCompletions\WPSetupStepCompletions;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;

/**
 * @method WPSetupStepCompletions find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistrCache = true)
 */
class InternalDBWPSetupStepCompletions extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBWPSetupStepCompletion::class;
    public const BASE_ENTITY_SET_CLASS = WPSetupStepCompletions::class;
}