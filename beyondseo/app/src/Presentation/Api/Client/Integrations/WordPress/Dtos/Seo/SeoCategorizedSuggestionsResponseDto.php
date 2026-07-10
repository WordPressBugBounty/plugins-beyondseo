<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Seo;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\CategorizedSuggestions;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class SeoCategorizedSuggestionsResponseDto
 *
 * Response DTO containing only categorized SEO suggestions
 */
class SeoCategorizedSuggestionsResponseDto extends RestResponseDto
{
    /** @var CategorizedSuggestions|null $categorizedSuggestions The categorized SEO suggestions */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public ?CategorizedSuggestions $categorizedSuggestions = null;
}
