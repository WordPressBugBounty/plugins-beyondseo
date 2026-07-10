<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Entities\Encryption;

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptions;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptionsTrait;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Encryption\DBEncryptionScopes;
use BeyondSEODeps\DDD\Domain\Common\Services\EncryptionScopesService;

/**
 * @property EncryptionScope[] $elements;
 * @method EncryptionScope getByUniqueKey(string $uniqueKey)
 * @method EncryptionScope first()
 * @method EncryptionScope[] getElements()
 * @method static EncryptionScopesService getService()
 * @method static DBEncryptionScopes getRepoClassInstance(?string $repoType = null)
 */
#[LazyLoadRepo(LazyLoadRepo::DB, DBEncryptionScopes::class)]
#[QueryOptions(top: 10)]
class EncryptionScopes extends EntitySet
{
    use QueryOptionsTrait;
}
