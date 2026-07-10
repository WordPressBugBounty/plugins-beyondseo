<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class MetaTagsGetRequestDto
 */
class MetaTagsGetRequestDto extends MetaTagsRequestDto {

    /** @var bool If set to true, debug mode will be activated */
    #[Parameter(in: Parameter::QUERY, required: false)]
    public bool $debug = false;
}