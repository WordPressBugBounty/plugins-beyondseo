<?php

declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Setup;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\SetupSteps\SetupStepCompletions\WPSetupStepCompletion;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RequestDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPSetupStepCompletionUpdateRequestDto
 */
class WPSetupStepCompletionUpdateRequestDto extends RequestDto
{
    /** @var WPSetupStepCompletion|null SetupStepCompletion object */
    #[Parameter(in: Parameter::BODY, required: true)]
    public ?WPSetupStepCompletion $setupStepCompletion = null;
}