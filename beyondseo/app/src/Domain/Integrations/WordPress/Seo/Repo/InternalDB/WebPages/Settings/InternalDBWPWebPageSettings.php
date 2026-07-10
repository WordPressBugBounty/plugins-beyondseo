<?php
declare( strict_types=1 );

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\WebPages\Settings;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Common\Repo\InternalDB\Models\InternalDBContentMetaModel;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Settings\WPWebPageSettings;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\WPWebPage;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Represents a set of WordPress user meta.
 * @method WPWebPageSettings find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = true)
 */
class InternalDBWPWebPageSettings extends InternalDBEntitySet
{
	public const BASE_REPO_CLASS = InternalDBWPWebPageSetting::class;
	public const BASE_ENTITY_SET_CLASS = WPWebPageSettings::class;

    /**
     * loads active Projects for account
     * @param WPWebPage $content
     * @param LazyLoad $lazyloadPropertyInstance
     * @return WPWebPageSettings|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws MappingException
     */
    public function lazyload(
        WPWebPage &$content,
        LazyLoad  &$lazyloadPropertyInstance
    ): ?WPWebPageSettings {
        $queryBuilder = self::createQueryBuilder();
        $queryBuilder
            ->where('postmeta.post_id = :post_id')
            // we collect just the meta data that is related to our plugin, actually just once that start with 'rankingcoach_'
            ->andWhere('postmeta.meta_key LIKE :meta_key')
            ->setParameter('meta_key', 'rankingcoach_%')
            ->setParameter('post_id', $content->id);
        return $this->find($queryBuilder, $lazyloadPropertyInstance->useCache);
    }

	/**
	 * Loads the list of a content-meta by content.
	 * @param int $contentId
	 * @return mixed
	 */
	public function findWebPageSettingsById(int $contentId): mixed
	{
		$queryBuilder = self::createQueryBuilder();
		$queryBuilder
			->select('cm')
			->from(InternalDBContentMetaModel::class, 'cm')
			->where('cm.post_id = :contentId')
			->setParameter('contentId', $contentId);
		return $queryBuilder->getQuery()->getResult();
	}
}