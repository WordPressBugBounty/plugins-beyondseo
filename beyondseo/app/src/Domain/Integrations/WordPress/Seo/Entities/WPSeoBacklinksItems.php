<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Entities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEODeps\DDD\Domain\Base\Entities\ObjectSet;

/**
 * Class WPSeoBacklinksItems
 * Collection of WPSeoBacklinksItem objects
 * 
 * @property WPSeoBacklinksItem[] $elements
 * @method WPSeoBacklinksItem getByUniqueKey(string $uniqueKey)
 * @method WPSeoBacklinksItem[] getElements()
 * @method WPSeoBacklinksItem first()
 */
class WPSeoBacklinksItems extends ObjectSet
{
}