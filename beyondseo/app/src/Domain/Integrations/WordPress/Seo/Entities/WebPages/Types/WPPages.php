<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Types;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\WPWebPages;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Services\WPWebPageService;

/**
 * Represents a set of WPPages pages.
 *
 * @method WPPage[] getElements()
 * @method WPPage|null first()
 * @method WPPage|null getByUniqueKey(string $uniqueKey)
 * @property WPPage[] $elements
 */
class WPPages extends WPWebPages
{
    public const SERVICE_NAME = WPWebPageService::class;
}