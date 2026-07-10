<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Flow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RequestDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPFlowPostSaveDataCompletionRequestDto
 */
class WPFlowPostSaveDataCompletionRequestDto extends RequestDto
{
    /** @var int $step The step that was requested */
    #[Parameter(in: Parameter::BODY, required: true)]
    public int $stepId;
}