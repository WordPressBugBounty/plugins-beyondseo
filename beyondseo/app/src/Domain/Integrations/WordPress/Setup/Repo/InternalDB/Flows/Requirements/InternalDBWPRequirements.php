<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB\Flows\Requirements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Requirements\WPRequirements;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Repository for onboarding requirements
 *
 * @method WPRequirements find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = false)
 */
class InternalDBWPRequirements extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBWPRequirement::class;
    public const BASE_ENTITY_SET_CLASS = WPRequirements::class;

    /**
     * @param bool $useCache
     * @return WPRequirements|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function getRequirements(bool $useCache = false): ?WPRequirements
    {
        $queryBuilder = static::createQueryBuilder();
        return $this->find($queryBuilder, $useCache);
    }


}