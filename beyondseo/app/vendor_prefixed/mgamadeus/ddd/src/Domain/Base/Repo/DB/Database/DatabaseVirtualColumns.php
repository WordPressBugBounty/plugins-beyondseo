<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Base\Repo\DB\Database;

use BeyondSEODeps\DDD\Domain\Base\Entities\ObjectSet;

/**
 * @method DatabaseModel getParent()
 * @property DatabaseVirtualColumn[] $elements;
 * @method DatabaseVirtualColumn getByUniqueKey(string $uniqueKey)
 * @method DatabaseVirtualColumn first()
 * @method DatabaseVirtualColumn[] getElements()
 */
class DatabaseVirtualColumns extends ObjectSet
{
}