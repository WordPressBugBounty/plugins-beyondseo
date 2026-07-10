<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Seo\Entities\WebPages\ContentElements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Seo\Entities\WebPages\WebPageContent;

/**
 * @method WebPageContent getParent()
 * @property WebPageContent $parent
 * @property Link[] $elements;
 * @method Link getByUniqueKey(string $uniqueKey)
 * @method Link[] getElements()
 */
class Links extends WebPageContentElements
{
    /** @var int The default maximum lengths for this content element */
    public const OPTIMAL_CONTENT_LENGTH = 70;

    public function getLinksTexts(): array
    {
        $linkTexts = [];
        foreach ($this->getElements() as $link) {
            $linkTexts[] = $link->content;
        }
        return $linkTexts;
    }
}