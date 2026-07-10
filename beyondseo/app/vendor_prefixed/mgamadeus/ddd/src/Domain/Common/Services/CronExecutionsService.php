<?php

declare (strict_types=1);
namespace BeyondSEODeps\DDD\Domain\Common\Services;

use BeyondSEODeps\DDD\Domain\Base\Services\EntitiesService;
use BeyondSEODeps\DDD\Domain\Common\Entities\Crons\Cron;
use BeyondSEODeps\DDD\Domain\Common\Entities\Crons\CronExecution;
use BeyondSEODeps\DDD\Domain\Common\Entities\Crons\CronExecutions;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons\DBCronExecution;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons\DBCronExecutions;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\NotFoundException;
use BeyondSEODeps\DDD\Infrastructure\Services\Service;
use BeyondSEODeps\Doctrine\ORM\Exception\ORMException;
use BeyondSEODeps\Doctrine\ORM\NonUniqueResultException;
use BeyondSEODeps\Doctrine\ORM\OptimisticLockException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
class CronExecutionsService extends EntitiesService
{
    public const DEFAULT_ENTITY_CLASS = CronExecution::class;
    /**
     * Lists all CronExecutions
     * @return \CronExecutions
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function list(): CronExecutions
    {
        $dbCronExecutions = new DBCronExecutions();
        return $dbCronExecutions->find();
    }
    /**
     * Returns last execution for Cron
     * @param \Cron $cron
     * @return CronExecution|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function getLastExecutionForCron(Cron &$cron): ?CronExecution
    {
        $dbCronExecutions = new DBCronExecutions();
        $queryBuilder = $dbCronExecutions::createQueryBuilder();
        $alias = $dbCronExecutions::getBaseModelAlias();
        $queryBuilder->where("{$alias}.cronId = :cronId");
        $queryBuilder->setParameter('cronId', $cron->id);
        $queryBuilder->orderBy("{$alias}.executionStartedAt", 'DESC');
        $queryBuilder->setMaxResults(1);
        $lastExecutions = $dbCronExecutions->find($queryBuilder);
        return $lastExecutions->first();
    }
}