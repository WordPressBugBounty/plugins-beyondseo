<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\TechnicalSeo;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Attributes\SeoMeta;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Configuration\WeightConfiguration;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\SearchEngineIndexation\RobotsMetaTagValidationOperation;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\SearchEngineIndexation\RobotsTxtValidationOperation;

/**
 * Class SearchEngineIndexationFactor
 *
 * This class is responsible for ensuring that the website is indexed by search engines.
 */
#[SeoMeta(
    name: 'Search Engine Indexation',
    weight: WeightConfiguration::WEIGHT_SEARCH_ENGINE_INDEXATION_FACTOR,
    description: 'Evaluates website indexability through search engine presence, robots.txt configuration, meta directives, and security status checks.',
)]

class SearchEngineIndexationFactor extends Factor
{
    /** @var class-string[] Operations */
    protected static array $operationsClasses = [
        RobotsTxtValidationOperation::class,
        RobotsMetaTagValidationOperation::class,
    ];
}
