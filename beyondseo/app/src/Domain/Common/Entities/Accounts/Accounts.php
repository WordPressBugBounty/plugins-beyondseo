<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Common\Entities\Accounts;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Services\AccountsService;
use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptions;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptionsTrait;

/**
 * @property Account[] $elements;
 * @method Account getByUniqueKey(string $uniqueKey)
 * @method Account first()
 * @method Account[] getElements()
 * @method static AccountsService getService()
 */
#[QueryOptions(top: 10)]
class Accounts extends EntitySet
{
    use QueryOptionsTrait;

    public const SERVICE_NAME = AccountsService::class;
}
