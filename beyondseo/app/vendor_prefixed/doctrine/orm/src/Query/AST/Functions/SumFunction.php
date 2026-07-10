<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Query\AST\Functions;

use BeyondSEODeps\Doctrine\ORM\Query\AST\AggregateExpression;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;

/**
 * "SUM" "(" ["DISTINCT"] StringPrimary ")"
 */
final class SumFunction extends FunctionNode
{
    /** @var AggregateExpression */
    private $aggregateExpression;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return $this->aggregateExpression->dispatch($sqlWalker);
    }

    public function parse(Parser $parser): void
    {
        $this->aggregateExpression = $parser->AggregateExpression();
    }
}
