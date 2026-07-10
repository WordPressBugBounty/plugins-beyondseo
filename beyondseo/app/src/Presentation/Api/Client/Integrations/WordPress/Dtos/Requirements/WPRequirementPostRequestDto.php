<?php

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Requirements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Requirements\WPRequirement;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RequestDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPRequirementPostRequestDto
 */

class WPRequirementPostRequestDto extends RequestDto
{
    #[Parameter(in: Parameter::BODY, required: true)]
    public WPRequirement $requirement;
}