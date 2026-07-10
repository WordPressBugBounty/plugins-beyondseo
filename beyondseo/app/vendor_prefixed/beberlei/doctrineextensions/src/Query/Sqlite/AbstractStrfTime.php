<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Sqlite;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

/** @author Tarjei Huse <tarjei.huse@gmail.com> */
abstract class AbstractStrfTime extends FunctionNode
{
    public $date;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return "STRFTIME('"
                . $this->getFormat()
                . "', "
                . $sqlWalker->walkArithmeticPrimary($this->date)
            . ')';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->date = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    abstract protected function getFormat(): string;
}
