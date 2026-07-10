<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Encryption;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\Doctrine\ORM\Mapping as ORM;
use BeyondSEODeps\Doctrine\ORM\PersistentCollection;
use DateTime;

#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[ORM\Table(name: 'EntityEncryptionScopes')]
class DBEncryptionScopeModel extends DoctrineModel
{
	public const MODEL_ALIAS = 'EncryptionScope';

	public const TABLE_NAME = 'EntityEncryptionScopes';

	public const ENTITY_CLASS = 'BeyondSEODeps\DDD\Domain\Common\Entities\Encryption\EncryptionScope';

	#[ORM\Column(type: 'string')]
	public ?string $scope;

	#[ORM\Column(type: 'string')]
	public ?string $description;

	#[ORM\Column(type: 'string')]
	public ?string $scopePassword;

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	public int $id;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $created;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $updated;

}