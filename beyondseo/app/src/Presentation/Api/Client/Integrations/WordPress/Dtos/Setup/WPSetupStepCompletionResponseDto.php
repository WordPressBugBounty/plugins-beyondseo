<?php

declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Setup;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\SetupSteps\SetupStepCompletions\WPSetupStepCompletion;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPSetupStepCompletionGetResponseDto
 */
class WPSetupStepCompletionResponseDto extends RestResponseDto
{
    /** @var WPSetupStepCompletion The SetupStepCompletion requested */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public WPSetupStepCompletion $setupStepCompletion;
}