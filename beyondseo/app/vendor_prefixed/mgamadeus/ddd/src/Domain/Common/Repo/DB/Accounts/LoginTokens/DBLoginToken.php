<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Common\Repo\DB\Accounts\LoginTokens;

use BeyondSEODeps\DDD\Domain\Base\Repo\DB\DBEntity;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineModel;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\EntityManagerFactory;
use BeyondSEODeps\DDD\Domain\Common\Entities\Accounts\LoginTokens\LoginToken;
use BeyondSEODeps\DDD\Infrastructure\Base\DateTime\DateTime;
use BeyondSEODeps\Doctrine\DBAL\Exception;
use ReflectionException;

/**
 * @method LoginToken find(DoctrineQueryBuilder|string|int $idOrQueryBuilder, bool $useEntityRegistryCache = true, ?DoctrineModel &$loadedOrmInstance = null, bool $deferredCaching = false)
 * @property DBLoginTokenModel $ormInstance
 */
class DBLoginToken extends DBEntity
{
    public const BASE_ENTITY_CLASS = LoginToken::class;
    public const BASE_ORM_MODEL = DBLoginTokenModel::class;

    /**
     * Deletes expired Tokens
     * @return void
     * @throws Exception
     * @throws ReflectionException
     */
    public function deleteExpiredTokens(): void
    {
        $connection = EntityManagerFactory::getInstance()->getConnection();
        $connection->executeStatement(
            'DELETE FROM ' . DBLoginTokenModel::getTableName() . ' WHERE usageLimit <= 0 OR validUntil < :currentDate',
            ['currentDate' => new DateTime('now')]
        );
    }
}