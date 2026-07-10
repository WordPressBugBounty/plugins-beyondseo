<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Seo;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Presentation\Base\Dtos\RequestDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class SeoPostRequestDto
 * @package BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Seo
 *
 * This class is used to handle the request for SEO analysis of a specific post.
 */
class SeoPostIdRequestDto extends RequestDto
{
    /** @var int $postId The post ID to analyze */
    #[Parameter(in: Parameter::PATH, required: true)]
    public int $postId;
}