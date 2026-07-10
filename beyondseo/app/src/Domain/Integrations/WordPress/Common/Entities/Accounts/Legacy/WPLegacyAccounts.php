<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts\Legacy;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Accounts\Accounts;
use BeyondSEO\Domain\Integrations\WordPress\Common\Repo\InternalDB\Accounts\Legacy\InternalDBWPLegacyAccounts;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * WordPress Accounts
 */
#[LazyLoadRepo(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBWPLegacyAccounts::class)]
class WPLegacyAccounts extends Accounts
{

}