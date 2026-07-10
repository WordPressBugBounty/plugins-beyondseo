<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Roles;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\DBEntitySet;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Domain\Common\Entities\Roles\Roles;

/**
 * @method Roles find(?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistrCache = true)
 */
class DBRoles extends DBEntitySet
{
    public const BASE_REPO_CLASS = DBRole::class;
    public const BASE_ENTITY_SET_CLASS = Roles::class;
}
