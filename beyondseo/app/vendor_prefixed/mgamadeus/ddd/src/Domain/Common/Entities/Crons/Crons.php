<?php

declare (strict_types=1);
namespace BeyondSEODeps\DDD\Domain\Common\Entities\Crons;

use BeyondSEODeps\DDD\Domain\Base\Entities\EntitySet;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptions;
use BeyondSEODeps\DDD\Domain\Base\Entities\QueryOptions\QueryOptionsTrait;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons\DBCrons;
use BeyondSEODeps\DDD\Domain\Common\Services\CronsService;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
/**
 * @property \Cron[] $elements;
 * @method Cron getByUniqueKey(string $uniqueKey)
 * @method Cron first()
 * @method Cron[] getElements()
 * @method static CronsService getService()
 * @method static DBCrons getRepoClassInstance(?string $repoType = null)
 */
#[LazyLoadRepo(LazyLoadRepo::DB, DBCrons::class)]
#[QueryOptions(top: 10)]
class Crons extends EntitySet
{
    use QueryOptionsTrait;
    /**
     * Executes all Crons and returns CronExecutions
     * @return \CronExecutions
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function execute(): CronExecutions
    {
        $cronExecutions = new CronExecutions();
        foreach ($this->getElements() as $cron) {
            $cronExecution = $cron->execute();
            if ($cronExecution) {
                $cronExecutions->add($cronExecution);
            }
        }
        return $cronExecutions;
    }
}
