<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntity;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\WPSetup;
use BeyondSEODeps\DDD\Domain\Base\Entities\DefaultObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use Psr\Cache\InvalidArgumentException;
use RankingCoach\Inc\Core\Base\BaseConstants;
use ReflectionException;

/**
 * Class InternalDBWPSetup
 * @method WPSetup find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = false, ?DoctrineModel &$loadedOrmInstance = null, bool $deferredCaching = true, array $initiatorClasses = [])
 */
//#[EntityCache(useExtendedRegistryCache: false, ttl: 300, cacheGroup: Cache::CACHE_GROUP_PHPFILES, cacheScopes: [])]
class InternalDBWPSetup extends InternalDBEntity
{
    public const BASE_ENTITY_CLASS = WPSetup::class;

    /**
     * @param DefaultObject $initiatingEntity
     * @param LazyLoad $lazyloadAttributeInstance
     *
     * @return WPSetup
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function lazyload(
        DefaultObject &$initiatingEntity,
        LazyLoad &$lazyloadAttributeInstance
    ): WPSetup {

        parent::lazyload($initiatingEntity, $lazyloadAttributeInstance);
        return $this->mapToEntity($lazyloadAttributeInstance->useCache);
    }

    /**
     * @param bool $useEntityRegistryCache
     * @return WPSetup
     * @throws ReflectionException
     */
    public function mapToEntity(bool $useEntityRegistryCache = true): WPSetup
    {
        /** @var WPSetup $setup */
        $setup = parent::mapToEntity($useEntityRegistryCache);

        $setup->isPluginOnboarded =
            get_option(BaseConstants::OPTION_ACCOUNT_ONBOARDING_ON_WP, false) == true &&
            !empty(get_option(BaseConstants::OPTION_ACCOUNT_ONBOARDING_ON_WP_LAST_UPDATE, null));
        $setup->lastPluginUpdate = (int)get_option(BaseConstants::OPTION_ACCOUNT_ONBOARDING_ON_WP_LAST_UPDATE, null);
        $setup->isApplicationOnboarded =
            get_option(BaseConstants::OPTION_ACCOUNT_ONBOARDING_ON_RC, false) == true &&
            !empty(get_option(BaseConstants::OPTION_ACCOUNT_ONBOARDING_ON_RC_LAST_UPDATE, null));
        $setup->lastApplicationUpdate = (int)get_option(BaseConstants::OPTION_ACCOUNT_ONBOARDING_ON_RC_LAST_UPDATE, null);

        return $setup;
    }
}