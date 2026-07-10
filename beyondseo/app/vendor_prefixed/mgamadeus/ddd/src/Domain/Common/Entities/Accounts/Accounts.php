<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Entities\Accounts;

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptions;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptionsTrait;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Accounts\DBAccounts;
use BeyondSEODeps\DDD\Domain\Common\Services\AccountsService;

/**
 * @property Account[] $elements;
 * @method Account getByUniqueKey(string $uniqueKey)
 * @method Account first()
 * @method Account[] getElements()
 * @method static AccountsService getService()
 */
#[LazyLoadRepo(LazyLoadRepo::DB, DBAccounts::class)]
#[QueryOptions(top: 10)]
class Accounts extends EntitySet
{
    use QueryOptionsTrait;

    public const SERVICE_NAME = AccountsService::class;
}