<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\TechnicalSeo;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Attributes\SeoMeta;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Configuration\WeightConfiguration;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\UseCanonicalTags\CanonicalTagValidationOperation;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\UseCanonicalTags\CrossDomainCanonicalCheckOperation;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\UseCanonicalTags\DuplicateContentDetectionOperation;

/**
 * Class UseCanonicalTagsFactor
 *
 * This class is responsible for ensuring that canonical tags are used correctly on a webpage.
 */
#[SeoMeta(
    name: 'Use Canonical Tags',
    weight: WeightConfiguration::WEIGHT_USE_CANONICAL_TAGS_FACTOR,
    description: 'Evaluates canonical tag implementation, cross-domain references, and duplicate content detection to prevent search engine indexing issues.',
)]
class UseCanonicalTagsFactor extends Factor
{
    /** @var class-string[] Operations */
    protected static array $operationsClasses = [
        CanonicalTagValidationOperation::class,
        CrossDomainCanonicalCheckOperation::class,
        DuplicateContentDetectionOperation::class,
    ];
}
