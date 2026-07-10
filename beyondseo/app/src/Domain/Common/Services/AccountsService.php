<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Common\Services;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Accounts\Account;
use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;

/**
 * @method static Account getEntityClassInstance()
 * @method Account getAccountByEmail(string $email)
 * @method Account find(string|int|null $accountId)
 * @method Account update(Entity $entity)
 * @method Account getAuthAccount()
 */
class AccountsService extends \BeyondSEODeps\DDD\Domain\Common\Services\AccountsService
{
    public const DEFAULT_ENTITY_CLASS = Account::class;

}