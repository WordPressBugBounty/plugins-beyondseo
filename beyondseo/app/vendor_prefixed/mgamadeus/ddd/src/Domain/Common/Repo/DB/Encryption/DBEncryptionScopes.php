<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Encryption;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\DBEntitySet;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Domain\Common\Entities\Encryption\EncryptionScopes;

/**
 * @method EncryptionScopes find(?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistrCache = true)
 */
class DBEncryptionScopes extends DBEntitySet
{
    public const BASE_REPO_CLASS = DBEncryptionScope::class;
    public const BASE_ENTITY_SET_CLASS = EncryptionScopes::class;
}
