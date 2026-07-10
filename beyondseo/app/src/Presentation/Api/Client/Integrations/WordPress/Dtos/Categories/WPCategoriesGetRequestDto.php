<?php

declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Categories;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RequestDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPCategoriesGetRequestDto
 */
class WPCategoriesGetRequestDto extends RequestDto
{
    /** @var string $search */
    #[Parameter(in: Parameter::QUERY, required: true)]
    public string $search;
}