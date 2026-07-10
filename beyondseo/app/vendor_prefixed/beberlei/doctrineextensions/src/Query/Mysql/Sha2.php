<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

/** @author Andreas Gallien <gallien@seleos.de> */
class Sha2 extends FunctionNode
{
    public $stringPrimary;

    public $simpleArithmeticExpression;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'SHA2(' .
            $sqlWalker->walkStringPrimary($this->stringPrimary) .
            ',' .
            $sqlWalker->walkSimpleArithmeticExpression($this->simpleArithmeticExpression) .
        ')';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->stringPrimary = $parser->StringPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->simpleArithmeticExpression = $parser->SimpleArithmeticExpression();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
