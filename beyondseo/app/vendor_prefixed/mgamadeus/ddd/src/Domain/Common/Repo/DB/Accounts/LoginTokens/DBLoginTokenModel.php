<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Accounts\LoginTokens;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\Doctrine\ORM\Mapping as ORM;
use BeyondSEODeps\Doctrine\ORM\PersistentCollection;
use DateTime;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Accounts\DBAccountModel;

#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[ORM\Table(name: 'EntityLoginTokens')]
class DBLoginTokenModel extends DoctrineModel
{
	public const MODEL_ALIAS = 'LoginToken';

	public const TABLE_NAME = 'EntityLoginTokens';

	public const ENTITY_CLASS = 'BeyondSEODeps\DDD\Domain\Common\Entities\Accounts\LoginTokens\LoginToken';

	#[ORM\Column(type: 'integer')]
	public ?int $accountId;

	#[ORM\Column(type: 'string')]
	public ?string $token;

	#[ORM\Column(type: 'integer')]
	public ?int $usageLimit;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $validUntil;

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	public int $id;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $created;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $updated;

	#[ORM\ManyToOne(targetEntity: DBAccountModel::class)]
	#[ORM\JoinColumn(name: 'accountId', referencedColumnName: 'id')]
	public ?DBAccountModel $account;

}