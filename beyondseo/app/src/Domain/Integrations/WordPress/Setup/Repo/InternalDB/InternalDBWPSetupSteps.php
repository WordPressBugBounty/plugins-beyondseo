<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\SetupSteps\WPSetupSteps;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;


/**
 * @method WPSetupSteps find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistrCache = true)
 */
class InternalDBWPSetupSteps extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBWPSetupStep::class;
    public const BASE_ENTITY_SET_CLASS = WPSetupSteps::class;
}