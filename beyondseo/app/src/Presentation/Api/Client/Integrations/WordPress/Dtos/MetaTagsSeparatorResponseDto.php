<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class MetaTagsSeparatorResponseDto
 */
class MetaTagsSeparatorResponseDto extends RestResponseDto {

	/** @var string $separator The separator */
	#[Parameter(in: Parameter::RESPONSE, required: true)]
	public string $separator;
}