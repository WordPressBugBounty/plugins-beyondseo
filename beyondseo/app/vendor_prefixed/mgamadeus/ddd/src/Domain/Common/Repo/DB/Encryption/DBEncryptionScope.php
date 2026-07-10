<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Encryption;

use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\DBEntity;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Domain\Common\Entities\Encryption\EncryptionScope;

/**
 * @method EncryptionScope find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = true, ?DoctrineModel &$loadedOrmInstance = null, bool $deferredCaching = false)
 * @method EncryptionScope update(Entity &$entity, int $depth = 1)
 * @property DBEncryptionScopeModel $ormInstance
 */
class DBEncryptionScope extends DBEntity
{
    public const BASE_ENTITY_CLASS = EncryptionScope::class;
    public const BASE_ORM_MODEL = DBEncryptionScopeModel::class;
}