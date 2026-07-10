<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Extracts;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Extracts\WPSetupExtractAuto;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPSetupExtractAutoResponseDto
 */
class WPSetupExtractAutoResponseDto extends RestResponseDto
{
    /** @var WPSetupExtractAuto|null */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public ?WPSetupExtractAuto $extracted = null;
}
