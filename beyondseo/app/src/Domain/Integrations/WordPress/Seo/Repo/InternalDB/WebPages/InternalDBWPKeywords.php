<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\WebPages;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Common\Repo\InternalDB\Models\InternalDBAppKeywordModel;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\Keywords\WPKeyword;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\WebPages\Content\Elements\ContentAnalysis\Keywords\WPKeywords;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Represents a set of WordPress keywords.
 *
 * @method WPKeywords find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = false)
 */
class InternalDBWPKeywords extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBWPKeyword::class;
    public const BASE_ENTITY_SET_CLASS = WPKeywords::class;

    /**
     * Loads all keywords.
     *
     * @return WPKeywords|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws MappingException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function getAllKeywords(): ?WPKeywords
    {
        $queryBuilder = self::createQueryBuilder();
        $queryBuilder->select('keyword')
            ->from(InternalDBAppKeywordModel::class, 'keyword')
            ->orderBy('keyword.id', 'DESC');
        return $this->find($queryBuilder);
    }

    /**
     * Gets a keyword entity by its hash.
     *
     * @param string $hash The hash of the keyword.
     * @return WPKeyword|null The keyword entity or null if not found.
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws MappingException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function getEntityByHash(string $hash, bool $useCache = false): ?WPKeyword
    {
        $queryBuilder = self::createQueryBuilder();
        $queryBuilder
            ->select('p')
            ->from(InternalDBAppKeywordModel::class, 'p')
            ->where('p.hash = :hash')
            ->setParameter('hash', $hash);
        return $this->find($queryBuilder, $useCache)->first();
    }
}