<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Entities\Accounts\LoginTokens;

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptions;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptionsTrait;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Accounts\LoginTokens\DBLoginToken;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Accounts\LoginTokens\DBLoginTokens;
use BeyondSEODeps\DDD\Domain\Common\Services\LoginTokensService;

/**
 * @property LoginToken[] $elements;
 * @method LoginToken getByUniqueKey(string $uniqueKey)
 * @method LoginToken first()
 * @method LoginToken[] getElements()
 * @method static DBLoginToken getRepoClassInstance(?string $repoType = null)
 * @method static LoginTokensService getService()
 */
#[LazyLoadRepo(LazyLoadRepo::DB, DBLoginTokens::class)]
#[QueryOptions(top: 10)]
class LoginTokens extends EntitySet
{
    use QueryOptionsTrait;
}
