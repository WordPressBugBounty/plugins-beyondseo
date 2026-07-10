<?php

declare (strict_types=1);
namespace BeyondSEODeps\DDD\Domain\Common\Services;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\EntityManagerFactory;
use BeyondSEODeps\DDD\Domain\Base\Services\EntitiesService;
use BeyondSEODeps\DDD\Domain\Common\Entities\Crons\Cron;
use BeyondSEODeps\DDD\Domain\Common\Entities\Crons\CronExecution;
use BeyondSEODeps\DDD\Domain\Common\Entities\Crons\Crons;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons\DBCron;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons\DBCronExecutionModel;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons\DBCronExecutions;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons\DBCrons;
use BeyondSEODeps\DDD\Infrastructure\Base\DateTime\DateTime;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\NotFoundException;
use BeyondSEODeps\DDD\Infrastructure\Services\Service;
use BeyondSEODeps\Doctrine\ORM\Exception\ORMException;
use BeyondSEODeps\Doctrine\ORM\NonUniqueResultException;
use BeyondSEODeps\Doctrine\ORM\OptimisticLockException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
class CronsService extends EntitiesService
{
    public const DEFAULT_ENTITY_CLASS = Cron::class;
    /**
     * Lists all Crons
     * @return \Crons
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function list(): Crons
    {
        $dbCrons = new DBCrons();
        return $dbCrons->find();
    }
    /**
     * @return void Deltes CronExecutions older than 14 days and deletes executions that are marked as running after 4 hours
     */
    public function cleanupCronExecutions(): void
    {
        $connection = EntityManagerFactory::getInstance()->getConnection();
        $connection->executeStatement('DELETE FROM ' . DBCronExecutionModel::getTableName() . " WHERE (state = '" . CronExecution::STATE_RUNNING . "' AND executionStartedAt <  :nowMinus4Hours) OR (state = '" . CronExecution::STATE_ENDED . "' AND executionStartedAt <  :nowMinus14Days)", ['nowMinus4Hours' => (new DateTime('now'))->modify('-4 hours'), 'nowMinus14Days' => (new DateTime('now'))->modify('-14 days')]);
    }
    /**
     * Returns Crons scheduled for execution, excluding running Crons
     * @return \Crons
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function getCronsScheduledForExecution(): Crons
    {
        $dbCrons = new DBCrons();
        $baseModelAlias = $dbCrons::getBaseModelAlias();
        $queryBuilder = $dbCrons::createQueryBuilder();
        $cronExecutionsAlias = DBCronExecutions::getBaseModelAlias();
        $cronExecutionsModel = DBCronExecutions::getBaseModel();
        $queryBuilder->andWhere("{$baseModelAlias}.id NOT IN (SELECT executions.cronId from {$cronExecutionsModel} executions WHERE executions.state = :stateRunning)");
        $queryBuilder->andWhere("{$baseModelAlias}.nextExecutionScheduledAt <= :currentDate");
        $queryBuilder->setParameter('currentDate', new DateTime());
        $queryBuilder->setParameter('stateRunning', CronExecution::STATE_RUNNING);
        return $dbCrons->find($queryBuilder);
    }
}