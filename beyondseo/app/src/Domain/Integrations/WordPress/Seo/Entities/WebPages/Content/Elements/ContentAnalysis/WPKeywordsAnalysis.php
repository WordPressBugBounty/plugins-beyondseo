<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Keywords\Keywords;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\Keywords\WPAdditionalKeywords;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\Keywords\WPPrimaryKeyword;
use BeyondSEODeps\DDD\Domain\Base\Entities\ValueObject;

/**
 * Class WPKeywordsAnalysis
 */
class WPKeywordsAnalysis extends ValueObject
{
    /**
     * @var WPPrimaryKeyword|null The matched primary keyword from existing content.
     */
    public ?WPPrimaryKeyword $primaryKeywordFromExisting;

    /**
     * @var WPPrimaryKeyword|null The matched primary keyword from the content being analyzed.
     */
    public ?WPPrimaryKeyword $primaryKeywordFromContent;

    /**
     * @var WPAdditionalKeywords|null Additional keywords from existing content.
     */
    public ?WPAdditionalKeywords $additionalKeywordsFromExisting;

    /**
     * @var WPAdditionalKeywords|null Additional keywords from the content being analyzed.
     */
    public ?WPAdditionalKeywords $additionalKeywordsFromContent;

    /**
     * @var Keywords|null Existing keywords related to the post.
     */
    public ?Keywords $existingKeywords;

    /**
     * WPKeywordsAnalysis constructor.
     *
     * @param WPPrimaryKeyword|null $matchPrimaryKeywordFromExisting The matched primary keyword from existing content.
     * @param WPPrimaryKeyword|null $matchPrimaryKeywordFromContent The matched primary keyword from the content.
     * @param Keywords|null $additionalKeywordsFromExisting Additional keywords from existing content.
     * @param Keywords|null $additionalKeywordsFromContent Additional keywords from the content.
     * @param Keywords|null $existingKeywords Existing keywords related to the post.
     */
    public function __construct(
        ?WPPrimaryKeyword $matchPrimaryKeywordFromExisting = null,
        ?WPPrimaryKeyword $matchPrimaryKeywordFromContent = null,
        ?Keywords         $additionalKeywordsFromExisting = null,
        ?Keywords         $additionalKeywordsFromContent = null,
        ?Keywords         $existingKeywords = null
    ) {
        $this->primaryKeywordFromExisting = $matchPrimaryKeywordFromExisting;
        $this->primaryKeywordFromContent = $matchPrimaryKeywordFromContent;
        $this->additionalKeywordsFromExisting = $additionalKeywordsFromExisting;
        $this->additionalKeywordsFromContent = $additionalKeywordsFromContent;
        $this->existingKeywords = $existingKeywords;

        parent::__construct();
    }
}