<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Settings;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Settings\Settings;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\WebPages\Settings\InternalDBWPWebPageSettings;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * WordPress Account Settings
 * @property WPWebPageSetting[] $elements;
 * @method WPWebPageSetting getByUniqueKey(string $uniqueKey)
 * @method WPWebPageSetting[] getElements
 * @method WPWebPageSetting first
 */
#[LazyLoadRepo(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBWPWebPageSettings::class)]
class WPWebPageSettings extends Settings
{

}