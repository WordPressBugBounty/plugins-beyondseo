<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Common\Services;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts\WPAccount;
use BeyondSEODeps\DDD\Domain\Base\Services\EntitiesService;

/**
 * Service for WPAccounts entities.
 *
 * @method static WPAccount getEntityClassInstance()
 */
class WPAccountService extends EntitiesService
{
    /** @var string DEFAULT_ENTITY_CLASS The default entity class. */
    public const DEFAULT_ENTITY_CLASS = WPAccount::class;
    
}