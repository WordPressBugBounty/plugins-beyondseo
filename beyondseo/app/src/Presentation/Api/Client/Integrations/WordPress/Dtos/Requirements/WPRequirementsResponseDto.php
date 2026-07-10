<?php

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Requirements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Requirements\WPRequirements;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

class WPRequirementsResponseDto extends RestResponseDto
{
    /** @var WPRequirements requested */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public WPRequirements $requirements;

}