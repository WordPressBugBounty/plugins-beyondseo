<?php

declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Categories;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Categories\WPCategories;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class WPCategoriesGetResponseDto
 */
class WPCategoriesGetResponseDto extends RestResponseDto{

    /** @var WPCategories|null */
    #[Parameter(in: Parameter::RESPONSE, required: false)]
    public ?WPCategories $categories = null;

}