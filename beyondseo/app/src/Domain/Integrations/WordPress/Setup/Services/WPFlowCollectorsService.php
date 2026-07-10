<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Services;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Collectors\WPFlowCollector;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Collectors\WPFlowCollectors;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB\Flows\Collectors\InternalDBWPFlowCollector;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB\Flows\Collectors\InternalDBWPFlowCollectors;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\DDD\Infrastructure\Services\Service;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Class WPFlowCollectorsService
 */
class WPFlowCollectorsService extends Service
{
    public const DEFAULT_ENTITY_CLASS = WPFlowCollector::class;

    /**
     * Get all collectors
     *
     * @return WPFlowCollectors|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws MappingException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function getCollectors(): ?WPFlowCollectors
    {
        $collectorsRepo = new InternalDBWPFlowCollectors();
        return $collectorsRepo->getAllCollectors();
    }

    /**
     * Get collector by id
     *
     * @param int $id
     * @return WPFlowCollector|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function getCollectorById(int $id): ?WPFlowCollector
    {
        $collectorsRepo = new InternalDBWPFlowCollector();
        return $collectorsRepo->getCollectorById($id);
    }
}