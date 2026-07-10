<?php

declare(strict_types=1);

namespace BeyondSEODeps\DDD\Domain\Base\Repo\DB\Database;

use BeyondSEODeps\DDD\Domain\Base\Entities\ObjectSet;

/**
 * @method DatabaseModel getParent()
 * @property DatabaseIndex[] $elements;
 * @method DatabaseIndex getByUniqueKey(string $uniqueKey)
 * @method DatabaseIndex first()
 * @method DatabaseIndex[] getElements()
 */
class DatabaseIndexes extends ObjectSet
{
    public function getIndexForSingleColumnName(string $columnName):?DatabaseIndex{
        return $this->getByUniqueKey(DatabaseIndex::uniqueKeyStatic($columnName));
    }
}