<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\ContentOptimisation;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Attributes\SeoMeta;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Configuration\WeightConfiguration;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\ContentReadability\AudienceTargetedAdjustmentsOperation;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\ContentReadability\ContentFormattingValidationOperation;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\ContentReadability\ReadabilityScoreValidationOperation;

/**
 * Class ContentReadabilityFactor
 *
 * This class is responsible for evaluating the readability of content on a page.
 */
#[SeoMeta(
    name: 'Content Readability',
    weight: WeightConfiguration::WEIGHT_CONTENT_READABILITY_FACTOR,
    description: 'Checks how easy the content is to read, how it is structured, and whether it suits the intended audience to improve engagement and clarity.',
)]
class ContentReadabilityFactor extends Factor
{
    /** @var class-string[] Operations */
    protected static array $operationsClasses = [
        ReadabilityScoreValidationOperation::class,
        ContentFormattingValidationOperation::class,
        AudienceTargetedAdjustmentsOperation::class,
    ];
}