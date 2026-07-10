<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Websites\Settings;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Settings\Setting;

/**
 * Class WPWebsiteGeneral
 */
class WPWebsiteGeneralSetting extends Setting
{
    /** @var string|null User's preferred timezone, or null if not set. */
    public ?string $timezone = null;

    /** @var string|null Defines the first day of the week, or null if not set. */
    public ?string $startOfWeek = null;

    /** @var string|null User's preferred date format, or null if not set. */
    public ?string $dateFormat = null;

    /** @var string|null User's preferred time format, or null if not set. */
    public ?string $timeFormat = null;
}