<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\WPSetup;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class OnboardingDataResponseDto
 *
 * @property bool $internal
 * @property bool $external
 */
class OnboardingDataResponseDto extends RestResponseDto {

    /** @var WPSetup $setupStep The plugin onboarding data, including the internal and external onboarding status */
    #[Parameter(in: Parameter::RESPONSE, required: false)]
	public WPSetup $setupData;
}