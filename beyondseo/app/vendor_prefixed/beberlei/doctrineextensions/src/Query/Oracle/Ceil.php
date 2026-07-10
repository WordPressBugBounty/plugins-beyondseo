<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Oracle;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

use function sprintf;

/** @author Jefferson Vantuir <jefferson.behling@gmail.com> */
class Ceil extends FunctionNode
{
    private $number;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'CEIL(%s)',
            $sqlWalker->walkArithmeticPrimary($this->number)
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->number = $parser->ArithmeticExpression();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
