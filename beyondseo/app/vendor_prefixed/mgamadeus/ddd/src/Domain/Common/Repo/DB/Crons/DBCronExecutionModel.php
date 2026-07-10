<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Crons;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\Doctrine\ORM\Mapping as ORM;
use BeyondSEODeps\Doctrine\ORM\PersistentCollection;
use DateTime;

#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[ORM\Table(name: 'EntityCronExecutions')]
class DBCronExecutionModel extends DoctrineModel
{
	public const MODEL_ALIAS = 'CronExecution';

	public const TABLE_NAME = 'EntityCronExecutions';

	public const ENTITY_CLASS = 'BeyondSEODeps\DDD\Domain\Common\Entities\Crons\CronExecution';

	#[ORM\Column(type: 'integer')]
	public ?int $cronId;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $executionStartedAt;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $executionEndedAt;

	#[ORM\Column(type: 'string')]
	public ?string $state;

	#[ORM\Column(type: 'string')]
	public ?string $executionState;

	#[ORM\Column(type: 'string')]
	public ?string $output;

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	public int $id;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $created;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $updated;

	#[ORM\ManyToOne(targetEntity: DBCronModel::class)]
	#[ORM\JoinColumn(name: 'cronId', referencedColumnName: 'id')]
	public ?DBCronModel $cron;

}