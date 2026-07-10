<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Public\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class MetaTagsGetResponseDto
 */
class PingGetResponseDto extends RestResponseDto {

	/** @var bool $ok The response message */
	#[Parameter(in: Parameter::RESPONSE, required: false)]
	public bool $ok = false;
}