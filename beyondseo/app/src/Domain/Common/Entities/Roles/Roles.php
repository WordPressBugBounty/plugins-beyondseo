<?php

namespace BeyondSEO\Domain\Common\Entities\Roles;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BeyondSEO\Domain\Common\Repo\InternalDB\Roles\InternalDBRoles;
use BeyondSEODeps\DDD\Domain\Base\Entities\Attributes\NoRecursiveUpdate;
use BeyondSEODeps\DDD\Domain\Base\Entities\LazyLoad\LazyLoadRepo;

/**
 * @property BeyondSEO\Domain\Common\Entities\Roles\Role[] $elements;
 * @method Role getByUniqueKey(string $uniqueKey)
 * @method Role[] getElements()
 */
#[LazyLoadRepo(LazyLoadRepo::INTERNAL_DB, InternalDBRoles::class)]
#[NoRecursiveUpdate]
class Roles extends \BeyondSEODeps\DDD\Domain\Common\Entities\Roles\Roles
{

    /**
     * @var Role[]
     */
    public array $elements = [];

    /**
     * Check if a role exists in the set.
     */
    public function hasRole(string $role): bool
    {
        foreach ($this->elements as $element) {
            if ($element->getName() === $role) {
                return true;
            }
        }
        return false;
    }
}