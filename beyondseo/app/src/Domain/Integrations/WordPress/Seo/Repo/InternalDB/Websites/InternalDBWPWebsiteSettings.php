<?php
declare( strict_types=1 );

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Websites;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntity;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Websites\Settings\WPWebsiteDiscussionSetting;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Websites\Settings\WPWebsiteGeneralSetting;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Websites\Settings\WPWebsiteReadingSetting;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Websites\WPWebsiteSetting;
use BeyondSEODeps\DDD\Domain\Base\Entities\DefaultObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Attributes\EntityCache;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Cache\Cache;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Represents a WordPress site options entity.
 * @method WPWebsiteSetting find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = false, ?DoctrineModel &$loadedOrmInstance = null, bool $deferredCaching = true, array $initiatorClasses = [])
 */
#[EntityCache(useExtendedRegistryCache: false, ttl: 300, cacheGroup: Cache::CACHE_GROUP_PHPFILES, cacheScopes: [])]
class InternalDBWPWebsiteSettings extends InternalDBEntity
{
    public const BASE_ENTITY_CLASS = WPWebsiteSetting::class;

    /**
     * @param DefaultObject $initiatingEntity
     * @param LazyLoad $lazyloadAttributeInstance
     *
     * @return WPWebsiteSetting|null
     * @throws ReflectionException
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     */
    public function lazyload(
        DefaultObject &$initiatingEntity,
        LazyLoad &$lazyloadAttributeInstance
    ): ?WPWebsiteSetting {
        parent::lazyload($initiatingEntity, $lazyloadAttributeInstance);
        return $this->mapToEntity($lazyloadAttributeInstance->useCache);
    }

    /**
     * MapToEntity
     * @throws ReflectionException
     */
    public function mapToEntity(bool $useEntityRegistryCache = false): WPWebsiteSetting
    {
        /** @var WPWebsiteSetting $siteOptions */
        $siteOptions = parent::mapToEntity($useEntityRegistryCache);

        $siteOptions->discussion = new WPWebsiteDiscussionSetting();
        $siteOptions->discussion->commentModeration = get_option('comment_moderation');
        $siteOptions->discussion->moderationKeys = get_option('moderation_keys');
        $siteOptions->discussion->defaultCommentStatus = get_option('default_comment_status');

        $siteOptions->general = new WPWebsiteGeneralSetting();
        $siteOptions->general->timezone = get_option('timezone_string')?: date_default_timezone_get();
        $siteOptions->general->startOfWeek = get_option('start_of_week');
        $siteOptions->general->dateFormat = get_option('date_format');
        $siteOptions->general->timeFormat = get_option('time_format');

        $siteOptions->reading = new WPWebsiteReadingSetting();
        $siteOptions->reading->showOnFront = get_option('show_on_front');
        $siteOptions->reading->pageOnFront = get_option('page_on_front') ? get_the_title(get_option('page_on_front')) : null;
        $siteOptions->reading->pageForPosts = get_option('page_for_posts') ? get_the_title(get_option('page_for_posts')) : null;
        $siteOptions->reading->postsPerPage = get_option('posts_per_page');

        return $siteOptions;
    }
}