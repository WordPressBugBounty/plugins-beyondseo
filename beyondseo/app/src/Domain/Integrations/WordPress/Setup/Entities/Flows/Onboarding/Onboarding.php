<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Onboarding;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Keywords\Keywords;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Requirements\WPRequirements;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\WPSetupSetting;
use BeyondSEODeps\DDD\Domain\Base\Entities\ValueObject;

/**
 * Class Onboarding
 */
class Onboarding extends ValueObject {

    /** @var WPRequirements|null requirements */
    public ?WPRequirements $requirements = null;

    /** @var Keywords|null keywords */
    public ?Keywords $keywords = null;

    /** @var int|null maxAllowedKeywords */
    public ?int $maxAllowedKeywords = null;

    /** @var WPSetupSetting|null setupSettings */
    public ?WPSetupSetting $setupSettings = null;
}