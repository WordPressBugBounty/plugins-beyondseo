<?php

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Requirements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Requirements\WPRequirement;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

class WPRequirementResponseDto extends RestResponseDto
{
    /** @var WPRequirement requested */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public WPRequirement $requirement;

}