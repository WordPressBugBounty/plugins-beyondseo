<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Base\Repo\DB\Database;

use BeyondSEODeps\DDD\Domain\Base\Entities\ObjectSet;

/**
 * @method DatabaseModelImport getParent()
 * @property DatabaseModelImport[] $elements;
 * @method DatabaseModelImport getByUniqueKey(string $uniqueKey)
 * @method DatabaseModelImport first()
 * @method DatabaseModelImport[] getElements()
 */
class DatabaseModelImports extends ObjectSet
{
}