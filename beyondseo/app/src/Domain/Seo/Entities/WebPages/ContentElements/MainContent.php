<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Seo\Entities\WebPages\ContentElements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Seo\Entities\WebPages\WebPageContent;

/**
 * @method WebPageContent getParent()
 * @property WebPageContent $parent
 */
class MainContent extends WebPageContentElement
{
    /** @var int The default optimal lengths for this content element */
    public const OPTIMAL_CONTENT_LENGTH = 2000;
}