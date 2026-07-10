<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB\Flows\Requirements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntity;
use BeyondSEO\Domain\Common\Repo\InternalDB\Models\InternalDBRequirementsModel;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Requirements\WPRequirement;
use BeyondSEODeps\DDD\Domain\Base\Entities\DefaultObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\DBAL\Exception;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Repository for WordPress flow questions
 * Class InternalDBWPRequirement
 *
 * @method WPRequirement find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = false, ?DoctrineModel &$loadedOrmInstance = null, bool $deferredCaching = true, array $initiatorClasses = [])
 * @method save(WPRequirement $requirement)
 * @property InternalDBRequirementsModel $ormInstance
 */
class InternalDBWPRequirement extends InternalDBEntity
{
    public const BASE_ENTITY_CLASS = WPRequirement::class;
    public const BASE_ORM_MODEL = InternalDBRequirementsModel::class;

    /**
     * Lazy loads a question entity
     *
     * @param DefaultObject $initiatingEntity The entity requesting the lazy load
     * @param LazyLoad $lazyloadAttributeInstance The LazyLoad attribute
     * @return WPRequirement The loaded question entity
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function lazyload(
        DefaultObject &$initiatingEntity,
        LazyLoad &$lazyloadAttributeInstance
    ): WPRequirement {
        parent::lazyload($initiatingEntity, $lazyloadAttributeInstance);
        return $this->mapToEntity($lazyloadAttributeInstance->useCache);
    }

    /**
     * Maps the ORM instance to an entity
     *
     * @param bool $useEntityRegistryCache Whether to use the registry cache
     * @return WPRequirement The mapped entity
     * @throws ReflectionException
     */
    public function mapToEntity(bool $useEntityRegistryCache = false): WPRequirement
    {
        /** @var WPRequirement $requirement */
        $requirement = parent::mapToEntity($useEntityRegistryCache);
        $requirement->id = $this->ormInstance->id;
        $requirement->setupRequirement = $this->ormInstance->setupRequirement;
        $requirement->entityAlias = $this->ormInstance->entityAlias;
        $requirement->value = $this->ormInstance->value;
        return $requirement;
    }

    /**
     * Maps an entity to the ORM instance
     *
     * @param WPRequirement $entity The entity to map
     * @return bool True if mapping was successful
     * @throws ReflectionException
     */
    protected function mapToRepository(Entity &$entity): bool
    {
        /** @var WPRequirement $entity */
        parent::mapToRepository($entity);
        $ormInstance = $this->ormInstance;

        if(isset($entity->id)) {
            $ormInstance->id = (int) $entity->id;
        }

        $ormInstance->setupRequirement = $entity->setupRequirement;

        if (isset($entity->entityAlias)) {
            $ormInstance->entityAlias = $entity->entityAlias;
        }


        if (isset($entity->value)) {
            $ormInstance->value = $entity->value;
        }

        $this->ormInstance = $ormInstance;
        return true;
    }

    /**
     * Finds a requirement by setup requirement
     * @param string $setupRequirement
     * @return WPRequirement
     * @throws BadRequestException
     * @throws Exception
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function findByRequirement(string $setupRequirement): WPRequirement {
        $qb = static::createQueryBuilder();
        $qb->where('rankingcoach_setup.setupRequirement = :setupRequirement')
            ->setParameter('setupRequirement', $setupRequirement);
        return $this->find($qb, false);
    }
}