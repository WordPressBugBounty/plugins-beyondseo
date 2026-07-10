<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Seo\Repo\InternalDB\Optimiser;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\OptimiserContext;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\Base\OptimiserContexts;
use BeyondSEO\Domain\Integrations\WordPress\Seo\Entities\Optimiser\SeoOptimiser;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Throwable;

/**
 * Class InternalDBSeoContexts
 *
 * This class is responsible for managing a collection of SEO contexts.
 * @method OptimiserContexts find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = false)
 */
class InternalDBSeoContexts extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBSeoContext::class;
    public const BASE_ENTITY_SET_CLASS = OptimiserContexts::class;

    /**
     * @throws MappingException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     * @throws ReflectionException
     * @throws InternalErrorException
     */
    public function lazyload(
        SeoOptimiser &$seoOptimiser,
        LazyLoad &$lazyloadPropertyInstance
    ): ?OptimiserContexts {
        return $this->getByAnalysisId($seoOptimiser->id, $lazyloadPropertyInstance->useCache);
    }

    /**
     * Get a context by its unique key
     * @param int $analysisId
     * @param bool $useCache
     * @return OptimiserContexts|null The context if found, null otherwise
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function getByAnalysisId(int $analysisId, bool $useCache = false): ?OptimiserContexts
    {
        $queryBuilder = self::createQueryBuilder();
        $queryBuilder
            ->where('seo_context.analysisId = :analysisId')
            ->setParameter('analysisId', $analysisId)
            ->orderBy('seo_context.id', 'ASC');

        return $this->find($queryBuilder, $useCache);
    }

    /**
     * Save the context to the database
     * @param OptimiserContexts $optimiserContexts The context to save
     * @return OptimiserContexts The saved context
     * @throws Throwable
     */
    public function save(OptimiserContexts $optimiserContexts): OptimiserContexts
    {
        /** @var OptimiserContext $optimiserContext */
        foreach ($optimiserContexts->elements as $optimiserContext) {
            /** @var InternalDBSeoContext $repo */
            $repo = $optimiserContext->getService()->getContextRepository();
            $repo->update($optimiserContext);
            $repo->save($optimiserContext);
        }
        return $optimiserContexts;
    }
}