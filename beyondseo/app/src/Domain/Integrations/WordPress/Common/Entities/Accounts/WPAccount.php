<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Accounts\Account;
use BeyondSEO\Domain\Common\Repo\InternalDB\Accounts\InternalDBAccount;
use BeyondSEO\Domain\Integrations\WordPress\Common\Services\WPAccountService;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * RankingCoach Account
 * @property WPAccounts $parent
 * @method WPAccounts getParent()
 * @method static WPAccountService getService()
 * @method static InternalDBAccount getRepoClassInstance( ?string $repoType = null)
 */
#[LazyLoadRepo(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBAccount::class)]
class WPAccount extends Account
{
    /** @var int|null $resellerId The reseller ID */
    public ?int $resellerId;

    /**
     * RankingCoachAppAccount constructor.
     */
    public function __construct()
    {
        // Initialize properties, potentially with defaults
        parent::__construct();
    }

    /**
     * Retrieves a unique key for the account.
     * @return string
     */
    public function uniqueKey(): string
    {
        // Generate a unique key based on certain account properties
        return md5($this->email . $this->externalId);
    }
}