<?php

namespace BeyondSEO\Domain\Integrations\WordPress\Common\Repo\InternalDB\Accounts\Legacy\Settings;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\InternalDBEntitySet;
use BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts\Legacy\Settings\WPLegacyAccountSettings;
use BeyondSEO\Domain\Integrations\WordPress\Common\Entities\Accounts\Legacy\WPLegacyAccount;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoad;
use BeyondSEODeps\DDD\Domain\Base\Repo\DB\Doctrine\DoctrineQueryBuilder;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\BadRequestException;
use BeyondSEODeps\DDD\Infrastructure\Exceptions\InternalErrorException;
use BeyondSEODeps\Doctrine\ORM\Mapping\MappingException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;

/**
 * WordPress Account
 * @method WPLegacyAccountSettings find( ?DoctrineQueryBuilder $queryBuilder = null, $useEntityRegistryCache = true)
 */
class InternalDBWPLegacyAccountSettings extends InternalDBEntitySet
{
    public const BASE_REPO_CLASS = InternalDBWPLegacyAccountSetting::class;
    public const BASE_ENTITY_SET_CLASS = WPLegacyAccountSettings::class;

    /**
     * loads active Projects for account
     * @param WPLegacyAccount $account
     * @param LazyLoad $lazyloadPropertyInstance
     * @return WPLegacyAccountSettings|null
     * @throws BadRequestException
     * @throws InternalErrorException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws MappingException
     */
    public function lazyload(
        WPLegacyAccount &$account,
        LazyLoad &$lazyloadPropertyInstance
    ): ?WPLegacyAccountSettings {
        $queryBuilder = self::createQueryBuilder();
        $queryBuilder
            ->where('usermeta.user_id = :current_user_id')
            ->setParameter('current_user_id', $account->id);
        return $this->find($queryBuilder, $lazyloadPropertyInstance->useCache);
    }
}