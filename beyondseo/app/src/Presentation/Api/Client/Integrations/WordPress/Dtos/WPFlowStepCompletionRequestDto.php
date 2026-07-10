<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Completions\WPFlowDataCompletion;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RequestDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPFlowStepCompletionRequestDto
 */
class WPFlowStepCompletionRequestDto extends RequestDto
{
    /** @var WPFlowDataCompletion $completion The step to save */
    #[Parameter(in: Parameter::BODY, required: true)]
    public WPFlowDataCompletion $completion;
}