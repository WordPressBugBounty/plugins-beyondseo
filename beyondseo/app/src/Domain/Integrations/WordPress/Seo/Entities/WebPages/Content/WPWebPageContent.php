<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\WPWebPage;
use BeyondSEO\Domain\Seo\Entities\WebPages\WebPageContent;

/**
 * @method WPWebPage getParent()
 * @property WPWebPage $parent
 *
 * WPWebPageContent class
 */
class WPWebPageContent extends WebPageContent
{
    /** @var int|null $postId The ID of the post that the content is for */
    public ?int $postId = null;

    /**
     * WPWebPageContent constructor.
     * @param int|null $postId
     */
    public function __construct(?int $postId = null)
    {
        $this->postId = $postId;
        parent::__construct();
    }
}