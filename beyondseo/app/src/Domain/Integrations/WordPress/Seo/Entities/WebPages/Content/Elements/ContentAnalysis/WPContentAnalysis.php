<?php
declare( strict_types=1 );

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts\Legacy\WPLegacyAccount;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\WPWebPage;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Entities\ValueObject;

/**
 * Class WPContentAnalysis
 */
class WPContentAnalysis extends ValueObject {

    /** @var int|null $postId The post ID */
    public ?int $postId;

    /** @var WPWebPage|null $post The post entity */
    #[LazyLoad]
    public ?WPWebPage $post = null;

    /** @var WPKeywordsAnalysis $keywordsAnalysis The keywords analysis from post content */
	public WPKeywordsAnalysis $keywordsAnalysis;

    /**
     * WPContentAnalysis constructor.
     *
     * @param int|null $postId The post ID
     */
    public function __construct(?int $postId = null, ?WPLegacyAccount $wpUser = null)
    {
        $this->postId = $postId;

        parent::__construct();
    }
}