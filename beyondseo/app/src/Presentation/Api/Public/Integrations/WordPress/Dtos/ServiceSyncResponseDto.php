<?php
declare( strict_types=1 );

namespace BeyondSEO\Presentation\Api\Public\Integrations\WordPress\Dtos;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class ServiceSyncResponseDto
 */
class ServiceSyncResponseDto extends RestResponseDto {

    /** @var object|null Set of synced keywords with verified externalIds */
    #[Parameter(in: Parameter::RESPONSE, required: false)]
    public ?object $keywords = null;

    /** @var bool $success Whether the sync was successful */
    #[Parameter(in: Parameter::RESPONSE, required: false)]
    public bool $success = false;

    /** @var string|null $message Error message if sync failed */
    #[Parameter(in: Parameter::RESPONSE, required: false)]
    public ?string $message = null;
}