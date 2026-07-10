<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Query\AST\Functions;

use BeyondSEODeps\Doctrine\DBAL\Types\Type;
use BeyondSEODeps\Doctrine\DBAL\Types\Types;
use BeyondSEODeps\Doctrine\ORM\Query\AST\AggregateExpression;
use BeyondSEODeps\Doctrine\ORM\Query\AST\TypedExpression;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;

/**
 * "COUNT" "(" ["DISTINCT"] StringPrimary ")"
 */
final class CountFunction extends FunctionNode implements TypedExpression
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

    public function getReturnType(): Type
    {
        return Type::getType(Types::INTEGER);
    }
}
