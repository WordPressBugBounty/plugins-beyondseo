<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Roles;

use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\DBEntity;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Domain\Common\Entities\Roles\Role;

/**
 * @method Role find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = true, ?DoctrineModel &$loadedOrmInstance = null, bool $deferredCaching = false)
 * @method Role update(Entity &$entity, int $depth = 1)
 * @property DBRoleModel $ormInstance
 */
class DBRole extends DBEntity
{
    public const BASE_ENTITY_CLASS = Role::class;
    public const BASE_ORM_MODEL = DBRoleModel::class;
}