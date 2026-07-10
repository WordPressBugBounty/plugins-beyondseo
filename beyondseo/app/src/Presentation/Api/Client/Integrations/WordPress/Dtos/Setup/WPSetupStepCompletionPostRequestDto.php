<?php

declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Setup;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RequestDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPSetupStepCompletionPostRequestDto
 */
class WPSetupStepCompletionPostRequestDto extends RequestDto
{
    /** @var int $stepNumber The requested step number */
    #[Parameter(in: Parameter::BODY, required: true)]
    public int $stepNumber = 1;
}