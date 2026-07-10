<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\CacheScopes\Invalidations;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\Doctrine\ORM\Mapping as ORM;
use BeyondSEODeps\Doctrine\ORM\PersistentCollection;
use DateTime;
use BeyondSEODeps\DDD\Domain\Common\Repo\DB\Accounts\DBAccountModel;

#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[ORM\Table(name: 'EntityCacheScopeInvalidations')]
class DBCacheScopeInvalidationModel extends DoctrineModel
{
	public const MODEL_ALIAS = 'CacheScopeInvalidation';

	public const TABLE_NAME = 'EntityCacheScopeInvalidations';

	public const ENTITY_CLASS = 'BeyondSEODeps\DDD\Domain\Common\Entities\CacheScopes\Invalidations\CacheScopeInvalidation';

	#[ORM\Column(type: 'string')]
	public ?string $cacheScope;

	#[ORM\Column(type: 'integer')]
	public ?int $accountId;

	#[ORM\Column(type: 'integer')]
	public ?int $numberOfTimesToInvalidateCache;

	#[ORM\Column(type: 'datetime')]
	public ?\DateTime $invalidateUntil;

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