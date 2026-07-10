<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;

/**
 * Class ModulesResponseDto
 * @property string[] $modules
 */
class ModulesResponseDto extends RestResponseDto {

	/**
     * @var string[] $modules
	 * The list of available modules.
     */
	public array $modules;
}