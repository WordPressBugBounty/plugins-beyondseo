<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class SocialMetaTagsGetResponseDto
 */
class SocialImageSourcesResponseDto extends RestResponseDto {

	/** @var array $image_sources The meta tag */
	#[Parameter(in: Parameter::RESPONSE, required: false)]
	public array $image_sources = [];
}