<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Query\Exec;

use BeyondSEODeps\Doctrine\DBAL\Connection;
use BeyondSEODeps\Doctrine\DBAL\Result;
use BeyondSEODeps\Doctrine\DBAL\Types\Type;

/**
 * SQL executor for a given, final, single SELECT SQL query
 *
 * @method string getSqlStatements()
 */
class FinalizedSelectExecutor extends AbstractSqlExecutor
{
    public function __construct(string $sql)
    {
        parent::__construct();

        $this->sqlStatements = $sql;
    }

    /**
     * @param list<mixed>|array<string, mixed>                                     $params
     * @param array<int, int|string|Type|null>|array<string, int|string|Type|null> $types
     */
    public function execute(Connection $conn, array $params, array $types): Result
    {
        return $conn->executeQuery($this->getSqlStatements(), $params, $types, $this->queryCacheProfile);
    }
}
