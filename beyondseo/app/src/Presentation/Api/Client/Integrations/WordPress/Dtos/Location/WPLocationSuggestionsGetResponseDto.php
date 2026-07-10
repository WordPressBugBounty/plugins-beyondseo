<?php

declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Location;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPLocationSuggestionsGetResponseDto
 */
class WPLocationSuggestionsGetResponseDto extends RestResponseDto
{
    /** @var array|null $businessLocationMatches */
    #[Parameter(in: Parameter::RESPONSE, required: false)]
    public ?array $businessLocationMatches = null;

    /** @var bool $success */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public bool $success = false;

    /** @var string|null $message */
    #[Parameter(in: Parameter::RESPONSE, required: false)]
    public ?string $message = null;
}