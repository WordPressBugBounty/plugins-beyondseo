<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

/** @author Pascal Wacker <hello@pascalwacker.ch> */
class AddTime extends FunctionNode
{
    public $date;

    public $time;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'ADDTIME(' . $sqlWalker->walkArithmeticPrimary($this->date) . ', ' . $sqlWalker->walkArithmeticPrimary($this->time) . ')';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->date = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_COMMA);

        $this->time = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
