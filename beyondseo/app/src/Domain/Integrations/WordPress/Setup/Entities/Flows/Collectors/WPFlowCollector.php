<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Collectors;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Steps\WPFlowSteps;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB\Flows\Collectors\InternalDBWPFlowCollector;
use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * @property WPFlowCollectors $parent
 * @method WPFlowCollectors getParent()
 */
#[LazyLoadRepo(repoType: LazyLoadRepo::INTERNAL_DB, repoClass: InternalDBWPFlowCollector::class)]
class WPFlowCollector extends Entity
{

    /** @var string The name of the FlowCollector */
    public string $collector;

    /** @var array The settings of the FlowCollector */
    public array $settings = [];

    /** @var string The class name of the FlowCollector */
    public string $className;

    /** @var int The priority of the FlowCollector */
    public int $priority;

    /** @var bool The active status of the FlowCollector */
    public bool $active;

    /** @var WPFlowSteps|null All available steps */
    #[LazyLoad(repoType: LazyLoadRepo::INTERNAL_DB)]
    public ?WPFlowSteps $steps = null;

    /** @var bool Whether this requirement should be saved in setup or not */
    public bool $saveCollectedData = true;

    /**
     * WPFlowCollector constructor.
     * @param int|null $id
     * @param mixed|null $settings
     */
    public function __construct(?int $id = null, array $settings = [])
    {
        parent::__construct();
        $this->id = $id;
        $this->settings = $settings;
    }

    /**
     * Get a setting value using dot notation path
     *
     * @param string $path Dot notation path (e.g., 'state.siteType.slug')
     * @param mixed $default Default value to return if path not found
     * @return mixed The setting value or default if not found
     */
    public function getSetting(string $path, mixed $default = null): mixed
    {
        $keys = explode('.', $path);
        $value = $this->settings;

        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return $default;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /** Placeholder methods for potential future implementation of business information collection.
     * These methods can be overridden in child classes to provide specific data collection logic.
     */
    public function businessEmailAddress() :?string {
        return null; // Placeholder for potential future implementation
    }

    /** Placeholder methods for potential future implementation of business information collection.
     * These methods can be overridden in child classes to provide specific data collection logic.
     */
    public function businessWebsiteUrl() :?string {
        return null; // Placeholder for potential future implementation
    }

    /** Placeholder methods for potential future implementation of business information collection.
     * These methods can be overridden in child classes to provide specific data collection logic.
     */
    public function businessName() :?string {
        return null; // Placeholder for potential future implementation
    }

    /** Placeholder methods for potential future implementation of business information collection.
     * These methods can be overridden in child classes to provide specific data collection logic.
     */
    public function businessDescription() :?string {
        return null; // Placeholder for potential future implementation
    }

    /** Placeholder methods for potential future implementation of business information collection.
     * These methods can be overridden in child classes to provide specific data collection logic.
     */
    public function businessServiceArea() :?string {
        return null; // Placeholder for potential future implementation
    }

    /** Placeholder methods for potential future implementation of business information collection.
     * These methods can be overridden in child classes to provide specific data collection logic.
     */
    public function businessAddress() :?string {
        return null; // Placeholder for potential future implementation
    }

    /** Placeholder methods for potential future implementation of business information collection.
     * These methods can be overridden in child classes to provide specific data collection logic.
     */
    public function businessGeoAddress() :?string {
        return null; // Placeholder for potential future implementation
    }

    /** Placeholder methods for potential future implementation of business information collection.
     * These methods can be overridden in child classes to provide specific data collection logic.
     */
    public function businessKeywords() :?string {
        return null; // Placeholder for potential future implementation
    }

    /** Placeholder methods for potential future implementation of business information collection.
    * These methods can be overridden in child classes to provide specific data collection logic.
    */
    public function businessCategories() :?string {
        return null; // Placeholder for potential future implementation
    }

    /** Placeholder methods for potential future implementation of business information collection.
     * These methods can be overridden in child classes to provide specific data collection logic.
     */
    public function businessSpecificDescription() :?string {
        return null;
    }

    /**
     * returns an individual unique key for current entity
     * @return string
     */
    public function uniqueKey(): string
    {
        $key = parent::uniqueKey();
        return md5($this->collector . '-' . $key);
    }
}