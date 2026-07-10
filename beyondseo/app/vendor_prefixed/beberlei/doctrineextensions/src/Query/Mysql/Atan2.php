<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

class Atan2 extends FunctionNode
{
    public $firstExpression;

    public $secondExpression;

    public function getSql(SqlWalker $sqlWalker): string
    {
        $firstArgument = $sqlWalker->walkSimpleArithmeticExpression(
            $this->firstExpression
        );

        $secondArgument = $sqlWalker->walkSimpleArithmeticExpression(
            $this->secondExpression
        );

        return 'ATAN2(' . $firstArgument . ', ' . $secondArgument . ')';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->firstExpression = $parser->SimpleArithmeticExpression();

        $parser->match(TokenType::T_COMMA);

        $this->secondExpression = $parser->SimpleArithmeticExpression();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
