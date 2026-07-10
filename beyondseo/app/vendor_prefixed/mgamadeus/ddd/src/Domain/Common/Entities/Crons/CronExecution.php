<?php

declare (strict_types=1);
namespace BeyondSEODeps\DDD\Domain\Common\Entities\Crons;

use BeyondSEODeps\DDD\Domain\Common\Entities\Roles\Role;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\RolesRequiredForUpdate;
use BeyondSEODeps\DDD\Domain\Base\Entities\ChangeHistory\ChangeHistoryTrait;
use BeyondSEODeps\DDD\Domain\Base\Entities\Entity;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptions;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptionsTrait;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Database\DatabaseColumn;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons\DBCronExecution;
use BeyondSEODeps\DDD\Domain\Common\Services\CronExecutionsService;
use BeyondSEODeps\DDD\Infrastructure\Base\DateTime\DateTime;
use BeyondSEODeps\DDD\Infrastructure\Validation\Constraints\Choice;
/**
 * @method \Crons getParent()
 * @property Crons $parent
 * @method static CronExecutionsService getService()
 * @method static DBCronExecution getRepoClassInstance(?string $repoType = null)
 */
#[LazyLoadRepo(LazyLoadRepo::DB, DBCronExecution::class)]
#[RolesRequiredForUpdate(Role::ADMIN)]
#[QueryOptions]
class CronExecution extends Entity
{
    use QueryOptionsTrait, ChangeHistoryTrait;
    /** @var string Cron is started and running */
    public const STATE_RUNNING = 'RUNNING';
    /** @var string Cron has been executed and ended */
    public const STATE_ENDED = 'ENDED';
    /** @var string Cron has been executed successfully */
    public const EXECUTION_STATE_SUCCESSFUL = 'SUCCESSFUL';
    /** @var string Cron has been executed with errors */
    public const EXECUTION_STATE_FAILED = 'FAILED';
    /** @var int The id of the executed Cron */
    public int $cronId;
    /** @var \Cron The executed Cron */
    #[LazyLoad(addAsParent: true)]
    public Cron $cron;
    /** @var DateTime The DateTime of the Cron's execution */
    public DateTime $executionStartedAt;
    /** @var DateTime The DateTime of the Cron's execution */
    public DateTime $executionEndedAt;
    /** @var string The running state of the CronExecution */
    #[Choice([self::STATE_RUNNING, self::STATE_ENDED])]
    public string $state;
    /** @var string The execution state of the CronExecution */
    #[Choice([self::EXECUTION_STATE_SUCCESSFUL, self::EXECUTION_STATE_FAILED])]
    public string $executionState;
    #[DatabaseColumn(sqlType: DatabaseColumn::SQL_TYPE_TEXT)]
    public string $output;
}
