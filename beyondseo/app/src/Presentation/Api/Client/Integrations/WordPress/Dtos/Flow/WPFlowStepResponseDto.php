<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Flow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Steps\WPFlowStep;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPFlowStepResponseDto
 */
class WPFlowStepResponseDto extends RestResponseDto
{
    /** @var WPFlowStep The FlowSteps requested */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public WPFlowStep $step;
}