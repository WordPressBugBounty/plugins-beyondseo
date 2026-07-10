<?php

namespace BeyondSEO\Domain\Common\Repo\InternalDB\Roles;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Entities\Accounts\Account;
use BeyondSEO\Domain\Common\Entities\Roles\Roles;
use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Common\Repo\InternalDB\Models\InternalDBRolesUserModel;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Attributes\EntityCache;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\EntityManagerFactory;
use BeyondSEODeps\DDD\Domain\Common\Entities\CacheScopes\CacheScope;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * @method Roles find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = true)
 */
#[EntityCache(useExtendedRegistryCache: true, ttl: 300, cacheScopes:[CacheScope::ACCOUNT_ROLES])]
class InternalDBRoles extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBRole::class;
    public const BASE_ENTITY_SET_CLASS = Roles::class;

    /**
     * @param Account $account
     * @param LazyLoad $lazyloadPropertyInstance
     * @return Roles|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function lazyload(Account &$account, LazyLoad &$lazyloadPropertyInstance): ?Roles
    {
        $em = EntityManagerFactory::getInstance();
        $expr = $em->getExpressionBuilder();
        $queryBuilder = self::createQueryBuilder()
            ->where(
                $expr->in(
                    'role.id',
                    self::createQueryBuilder()
                        ->select('RolesUser.role_id')
                        ->from(InternalDBRolesUserModel::class, 'RolesUser')
                        ->where('RolesUser.user_id = :user_id')->getDQL()
                )
            )->setParameter('user_id', $account->id);
        // an alternative is:
        //->where('role.id in(Select RolesUser.role_id from '.RolesUserModel::class.' RolesUser where RolesUser.user_id = :user_id)')
        return $this->find($queryBuilder, $lazyloadPropertyInstance->useCache);
    }
}