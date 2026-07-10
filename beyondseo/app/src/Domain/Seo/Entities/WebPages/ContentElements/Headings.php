<?php

declare(strict_types=1);

namespace BeyondSEO\Domain\Seo\Entities\WebPages\ContentElements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Seo\Entities\WebPages\WebPageContent;

/**
 * @method WebPageContent getParent()
 * @property WebPageContent $parent
 * @property Heading[] $elements;
 * @method Heading getByUniqueKey(string $uniqueKey)
 * @method Heading[] getElements()
 */
class Headings extends WebPageContentElements
{
    /** @var int The default maximum lengths for this content element */
    public const OPTIMAL_CONTENT_LENGTH = 70;

    public function getConcatenatedHeadingsDetails(): string
    {
        $concatenatedHeadings = '';
        foreach ($this->getElements() as $heading) {
            $concatenatedHeadings .= $heading->headingType . ': ' .  $heading->content . ', ';
        }

        return $concatenatedHeadings;
    }
}