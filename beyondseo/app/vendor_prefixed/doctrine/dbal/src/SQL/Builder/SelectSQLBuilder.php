<?php

namespace BeyondSEODeps\Doctrine\DBAL\SQL\Builder;

use BeyondSEODeps\Doctrine\DBAL\Exception;
use BeyondSEODeps\Doctrine\DBAL\Query\SelectQuery;

interface SelectSQLBuilder
{
    /** @throws Exception */
    public function buildSQL(SelectQuery $query): string;
}
