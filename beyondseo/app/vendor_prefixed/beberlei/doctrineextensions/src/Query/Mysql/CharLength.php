<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

/** @author Metod <metod@simpel.si> */
class CharLength extends FunctionNode
{
    private $expr1;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'CHAR_LENGTH(' . $sqlWalker->walkArithmeticPrimary($this->expr1) . ')';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->expr1 = $parser->ArithmeticExpression();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
