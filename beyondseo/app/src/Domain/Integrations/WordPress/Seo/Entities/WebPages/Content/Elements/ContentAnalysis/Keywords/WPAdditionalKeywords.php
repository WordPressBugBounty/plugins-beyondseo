<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\Keywords;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Keywords\Keyword;

/**
 * Class WPAdditionalKeywords
 */
class WPAdditionalKeywords extends WPKeywords
{
    /**
     * @var Keyword[] $elements
     */
    public array $elements = [];
}