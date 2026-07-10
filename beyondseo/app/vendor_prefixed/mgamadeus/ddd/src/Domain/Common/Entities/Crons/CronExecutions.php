<?php

declare (strict_types=1);
namespace BeyondSEODeps\DDD\Domain\Common\Entities\Crons;

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptions;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptionsTrait;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons\DBCronExecutions;
use BeyondSEODeps\DDD\Domain\Common\Services\CronExecutionsService;
/**
 * @property \Cron[] $elements;
 * @method CronExecution getByUniqueKey(string $uniqueKey)
 * @method CronExecution first()
 * @method CronExecution[] getElements()
 * @method static CronExecutionsService getService()
 * @method static DBCronExecutions getRepoClassInstance(?string $repoType = null)
 */
#[LazyLoadRepo(LazyLoadRepo::DB, DBCronExecutions::class)]
#[QueryOptions(top: 10)]
class CronExecutions extends EntitySet
{
    use QueryOptionsTrait;
}
