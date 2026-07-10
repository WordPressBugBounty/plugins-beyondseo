<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\PerformanceAndSpeed;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Attributes\SeoMeta;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Configuration\WeightConfiguration;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\AltTextToImages\AltTextPresenceCheckOperation;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\AltTextToImages\DescriptiveAltTextOperation;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\AltTextToImages\PrimaryKeywordInAltTextOperation;

/**
 * Class AltTextToImagesFactor
 *
 * This class is responsible for ensuring that all images on the website have an appropriate alt text.
 */
#[SeoMeta(
    name: 'Alt Text To Images',
    weight: WeightConfiguration::WEIGHT_ALT_TEXT_TO_IMAGES_FACTOR,
    description: 'Analyzes alt text presence, keyword integration, and descriptive clarity to assess image accessibility and enhance SEO performance.',
)]
class AltTextToImagesFactor extends Factor
{
    /** @var class-string[] Operations */
    protected static array $operationsClasses = [
        AltTextPresenceCheckOperation::class,
        PrimaryKeywordInAltTextOperation::class,
        DescriptiveAltTextOperation::class
    ];
}