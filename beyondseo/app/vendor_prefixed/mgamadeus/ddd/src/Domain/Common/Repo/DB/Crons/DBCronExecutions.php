<?php

declare (strict_types=1);
namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\DBEntitySet;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Domain\Common\Entities\Crons\CronExecutions;
/**
 * @method \CronExecutions find(?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistrCache = true)
 */
class DBCronExecutions extends DBEntitySet
{
    public const BASE_REPO_CLASS = DBCronExecution::class;
    public const BASE_ENTITY_SET_CLASS = CronExecutions::class;
}
