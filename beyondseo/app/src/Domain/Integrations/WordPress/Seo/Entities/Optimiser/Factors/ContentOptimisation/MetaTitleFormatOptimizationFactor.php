<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Attributes\SeoMeta;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Configuration\WeightConfiguration;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\MetaTitleFormatOptimization\MetaTitleLengthCheckOperation;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\MetaTitleFormatOptimization\MetaTitleQualityAnalyzerOperation;

/**
 * Class MetaTitleFormatOptimizationFactor
 *
 * This class is responsible for creating a meta-title tag for SEO optimization.
 */
#[SeoMeta(
    name: 'Meta Title Format Optimization',
    weight: WeightConfiguration::WEIGHT_META_TITLE_FORMAT_OPTIMIZATION_FACTOR,
    description: 'Evaluates meta title length and quality, ensuring optimal character count and structure for search engine visibility.',
)]
class MetaTitleFormatOptimizationFactor extends Factor
{
    /** @var class-string[] Operations */
    protected static array $operationsClasses = [
        MetaTitleLengthCheckOperation::class,
        MetaTitleQualityAnalyzerOperation::class,
    ];
}