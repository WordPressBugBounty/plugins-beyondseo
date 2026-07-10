<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Collectors\Data;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Collectors\WPFlowCollector;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\WPFlowRequirements;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Services\WPRequirementsService;
use BeyondSEO\Infrastructure\Services\AppService;

/**
 * Class DatabaseDataCollector
 */
class DatabaseDataCollector extends WPFlowCollector
{
    public string $collector = WPFlowRequirements::SETUP_COLLECTOR_DATABASE;

    /** @var bool if collected data gathered be saved */
    public bool $saveCollectedData = false;

    /**
     * @var WPRequirementsService|mixed
     */
    private WPRequirementsService $wpRequirementsService;

    /**
     * DatabaseDataCollector constructor.
     * @param int|null $id
     * @param mixed|null $settings
     */
    public function __construct(?int $id = null, array $settings = [])
    {
        $this->wpRequirementsService = AppService::instance()->getService(WPRequirementsService::class);
        $requirements = WPFlowRequirements::allRequirements();
        $this->createDynamicMethods($requirements);
        parent::__construct($id, $settings);
    }

    /**
     * Dynamically creates methods for each requirement.
     * Each method will return the value of the corresponding requirement from the WPRequirementsService.
     *
     * @param array $requirements An array of requirement names for which to create methods.
     */
    public function createDynamicMethods(array $requirements): void
    {
        foreach ($requirements as $requirement) {
            if (!method_exists($this, $requirement)) {
                $this->{$requirement} = function () use ($requirement) {
                    return $this->wpRequirementsService->getRequirement($requirement)?->value;
                };
            }
        }
    }

    /**
     * Magic method to handle calls to dynamically created methods.
     * If a method exists as a property and is callable, it will be invoked with the provided arguments.
     *
     * @param string $name The name of the method being called.
     * @param array $arguments The arguments passed to the method.
     * @return mixed|null The result of the callable method or null if it doesn't exist.
     */
    public function __call($name, $arguments) {
        if (isset($this->{$name}) && is_callable($this->{$name})) {
            return call_user_func_array($this->{$name}, $arguments);
        }
        return null;
    }

}