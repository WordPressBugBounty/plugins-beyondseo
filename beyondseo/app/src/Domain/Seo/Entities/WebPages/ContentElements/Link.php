<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Seo\Entities\WebPages\ContentElements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @method Links getParent()
 * @property Links $parent
 */
class Link extends WebPageContentElement
{
    /** @var string The href property of the Link */
    public ?string $href;
}