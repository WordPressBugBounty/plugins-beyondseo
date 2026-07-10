<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Flow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Steps\WPFlowSteps;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPFlowStepsResponseDto
 */
class WPFlowStepsResponseDto extends RestResponseDto
{
    /** @var WPFlowSteps The FlowSteps requested */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public WPFlowSteps $steps;

    /** @var bool Indicates if the address requirement should be prefilled */
    #[Parameter(in: Parameter::RESPONSE, required: false)]
    public bool $prefillAddressRequirement = false;
}