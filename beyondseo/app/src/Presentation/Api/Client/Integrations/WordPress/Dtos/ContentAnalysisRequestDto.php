<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RequestDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class ContentAnalysisRequestDto
 */
class ContentAnalysisRequestDto extends RequestDto
{
    /** @var int $postId The post-ID */
    #[Parameter(in: Parameter::PATH, required: true)]
    public int $postId;

    /** @var bool If set to true, debug mode will be activated */
    #[Parameter(in: Parameter::QUERY, required: false)]
    public bool $debug = false;
}