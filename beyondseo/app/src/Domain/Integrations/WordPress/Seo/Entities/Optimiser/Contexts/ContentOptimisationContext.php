<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Contexts;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Attributes\SeoMeta;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Configuration\WeightConfiguration;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\OptimiserContext;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\AssignKeywordsFactor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\ContentQualityAndLengthFactor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\ContentReadabilityFactor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\FirstParagraphKeywordUsageFactor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\HeaderTagsStructureFactor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\MetaDescriptionFormatOptimizationFactor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\MetaDescriptionKeywordsFactor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\MetaTitleFormatOptimizationFactor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\MetaTitleKeywordsFactor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation\PageContentKeywordsFactor;

/**
 * Class ContentOptimisationContext
 * 
 * Represents the content optimization context for SEO analysis.
 */
#[SeoMeta(
    name: 'Content Optimisation',
    weight: WeightConfiguration::WEIGHT_CONTENT_OPTIMISATION_CONTEXT,
    description: 'Analyzes and optimizes content for SEO by focusing on keyword usage, content quality, readability, and meta tags.',
)]
class ContentOptimisationContext extends OptimiserContext
{
    /** @var array $contextFactors List of SEO factors that are part of this context */
    protected static array $contextFactors = [
        AssignKeywordsFactor::class,
        MetaTitleKeywordsFactor::class,
        MetaDescriptionKeywordsFactor::class,
        PageContentKeywordsFactor::class,
        FirstParagraphKeywordUsageFactor::class,
        HeaderTagsStructureFactor::class,
        MetaTitleFormatOptimizationFactor::class,
        MetaDescriptionFormatOptimizationFactor::class,
        ContentQualityAndLengthFactor::class,
        ContentReadabilityFactor::class,
    ];
}