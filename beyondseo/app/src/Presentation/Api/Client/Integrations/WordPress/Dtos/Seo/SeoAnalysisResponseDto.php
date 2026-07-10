<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Seo;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Analysis\Results\SEOAnalysisResult;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class SeoAnalysisResponseDto
 */
class SeoAnalysisResponseDto extends RestResponseDto
{
    /** @var object|null $analyseResult The SEO analysis results */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public ?object $analyseResult = null;
}