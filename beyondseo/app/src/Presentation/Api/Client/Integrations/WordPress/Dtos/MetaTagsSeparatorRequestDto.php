<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class MetaTagsSeparatorRequestDto
 */
class MetaTagsSeparatorRequestDto extends MetaTagsRequestDto {

	/** @var string|null $separator The post-separator */
	#[Parameter(in: Parameter::BODY, required: false)]
	public ?string $separator = null;
}