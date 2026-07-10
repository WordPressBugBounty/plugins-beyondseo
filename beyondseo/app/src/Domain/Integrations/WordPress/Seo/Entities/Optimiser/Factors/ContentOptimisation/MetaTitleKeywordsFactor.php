<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Attributes\SeoMeta;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Configuration\WeightConfiguration;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\MetaTitleKeywords\PrimaryKeywordCheckOperation;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\MetaTitleKeywords\SecondaryKeywordsCheckOperation;

/**
 * Class MetaTitleKeywordsFactor
 *
 * This class represents a factor for checking the presence of keywords in the Meta Title.
 * It extends the base Factor class and can be used to evaluate the effectiveness of keyword assignment.
 */
#[SeoMeta(
    name: 'Meta Title Keywords',
    weight: WeightConfiguration::WEIGHT_META_TITLE_KEYWORDS_FACTOR,
    description: 'Evaluates primary and secondary keyword placement in meta titles, prioritizing optimal positioning for maximum search visibility.',
)]
class MetaTitleKeywordsFactor extends Factor
{
    /** @var class-string[] Operations */
    protected static array $operationsClasses = [
        PrimaryKeywordCheckOperation::class,
        SecondaryKeywordsCheckOperation::class,
    ];
}