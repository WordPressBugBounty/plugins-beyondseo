<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

/** @author Dawid Nowak <macdada@mmg.pl> */
class Hour extends FunctionNode
{
    public $date;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'HOUR(' . $sqlWalker->walkArithmeticPrimary($this->date) . ')';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->date = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
