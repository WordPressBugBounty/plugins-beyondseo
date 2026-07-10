<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts\Legacy\Settings;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Settings\Settings;
use BeyondSEO\Domain\Integrations\WordPress\Common\Repo\InternalDB\Accounts\Legacy\Settings\InternalDBWPLegacyAccountSettings;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * WordPress Account Settings
 * @property WPLegacyAccountSetting[] $elements;
 * @method WPLegacyAccountSetting getByUniqueKey(string $uniqueKey)
 * @method WPLegacyAccountSetting[] getElements
 * @method WPLegacyAccountSetting first
 */
#[LazyLoadRepo(LazyLoadRepo::INTERNAL_DB, InternalDBWPLegacyAccountSettings::class)]
class WPLegacyAccountSettings extends Settings
{
    public object $settings;
}