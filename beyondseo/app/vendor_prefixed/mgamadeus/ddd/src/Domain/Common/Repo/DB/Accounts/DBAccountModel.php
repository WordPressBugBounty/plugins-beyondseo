<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Accounts;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\Doctrine\ORM\Mapping as ORM;
use BeyondSEODeps\Doctrine\ORM\PersistentCollection;
use DateTime;

#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[ORM\Table(name: 'EntityAccounts')]
class DBAccountModel extends DoctrineModel
{
	public const MODEL_ALIAS = 'Account';

	public const TABLE_NAME = 'EntityAccounts';

	public const ENTITY_CLASS = 'BeyondSEODeps\DDD\Domain\Common\Entities\Accounts\Account';

	#[ORM\Column(type: 'string')]
	public ?string $password;

	#[ORM\Column(type: 'string')]
	public ?string $email;

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	public int $id;

}