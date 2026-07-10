<?php
declare(strict_types=1);

namespace BeyondSEO\Presentation\Api\Client\Integrations\WordPress\Dtos\Seo;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Models\Results\OptimiserResult;
use BeyondSEODeps\DDD\Presentation\Base\Dtos\RestResponseDto;
use BeyondSEODeps\DDD\Presentation\Base\OpenApi\Attributes\Parameter;

/**
 * Class SeoOptimiserResponseDto
 */
class SeoOptimiserResponseDto extends RestResponseDto
{
    /** @var OptimiserResult|null $analyseResult The SEO analysis results */
    #[Parameter(in: Parameter::RESPONSE, required: true)]
    public ?OptimiserResult $analyseResult = null;
}