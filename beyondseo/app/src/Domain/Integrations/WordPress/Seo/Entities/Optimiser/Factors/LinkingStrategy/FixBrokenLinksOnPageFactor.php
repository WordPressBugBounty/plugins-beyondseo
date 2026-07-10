<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Factors\LinkingStrategy;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Attributes\SeoMeta;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Configuration\WeightConfiguration;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\Factor;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Operations\FixBrokenLinksOnPage\BrokenLinksIdentificationOperation;

/**
 * Class FixBrokenLinksOnPageFactor
 *
 * This class is responsible for fixing broken links on a webpage.
 */
#[SeoMeta(
    name: 'Fix Broken Links On Page',
    weight: WeightConfiguration::WEIGHT_FIX_BROKEN_LINKS_ON_PAGE_FACTOR,
    description: 'Identifies broken internal and external links, prioritizing critical issues affecting user experience and SEO performance. If no links are found on the page, this check is marked as passed by default.',
)]
class FixBrokenLinksOnPageFactor extends Factor
{
    /** @var class-string[] Operations */
    protected static array $operationsClasses = [
        BrokenLinksIdentificationOperation::class
    ];
}