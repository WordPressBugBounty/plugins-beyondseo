<?php
declare(strict_types=1);

namespace BeyondSEO\Domain\Integrations\WordPress\Setup\Repo\InternalDB\Flows\Completions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Completions\WPFlowDataCompletions;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Questions\WPFlowQuestion;
use BeyondSEO\Domain\Integrations\WordPress\Setup\Entities\Flows\Steps\WPFlowStep;
use BeyondSEODeps\DDD\Domain\Base\Entities\DefaultObject;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * Class InternalDBWPFlowCollectors
 * @method WPFlowDataCompletions find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = true)
 */
class InternalDBWPFlowCompletions extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBWPFlowCompletion::class;
    public const BASE_ENTITY_SET_CLASS = WPFlowDataCompletions::class;

    /**
     * @param DefaultObject $initiatingEntity
     * @param LazyLoad $lazyloadPropertyInstance
     * @return WPFlowDataCompletions|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws MappingException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function lazyload(
        DefaultObject &$initiatingEntity,
        LazyLoad &$lazyloadPropertyInstance
    ): ?WPFlowDataCompletions {
        return $this->getAllCompletions($lazyloadPropertyInstance->useCache);
    }

    /**
     * @param bool $useCache
     * @return WPFlowDataCompletions
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function getAllCompletions(bool $useCache = true): WPFlowDataCompletions
    {
        $queryBuilder = static::createQueryBuilder();
        $queryBuilder
            ->select('flow_completion')
            ->orderBy('flow_completion.timeOfCompletion', 'DESC');
        return $this->find($queryBuilder, $useCache);
    }

    /**
     * @param WPFlowStep $step
     * @param LazyLoad $lazyloadAttributeInstance
     * @return WPFlowDataCompletions|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function getCompletionByStepId(
        WPFlowStep &$step,
        LazyLoad &$lazyloadAttributeInstance
    ): ?WPFlowDataCompletions
    {
        $queryBuilder = static::createQueryBuilder();
        $queryBuilder
            ->select('flow_completion')
            ->where('flow_completion.stepId = :stepId')
            ->setParameter('stepId', $step->id)
            ->orderBy('flow_completion.timeOfCompletion', 'DESC');
        return $this->find($queryBuilder, $lazyloadAttributeInstance->useCache);
    }

    /**
     * @param WPFlowQuestion $question
     * @param LazyLoad $lazyloadAttributeInstance
     * @return WPFlowDataCompletions|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws MappingException
     * @throws ReflectionException
     */
    public function getCompletionsByQuestionId(
        WPFlowQuestion &$question,
        LazyLoad &$lazyloadAttributeInstance
    ): ?WPFlowDataCompletions
    {
        $queryBuilder = static::createQueryBuilder();
        $queryBuilder
            ->select('flow_completion')
            ->where('flow_completion.questionId = :questionId')
            ->setParameter('questionId', $question->id)
            ->orderBy('flow_completion.timeOfCompletion', 'DESC');
        return $this->find($queryBuilder, $lazyloadAttributeInstance->useCache);
    }
}