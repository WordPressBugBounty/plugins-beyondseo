<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

use function sprintf;

class SubstringIndex extends FunctionNode
{
    public $string = null;

    public $delimiter = null;

    public $count = null;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->string = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->delimiter = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->count = $parser->ArithmeticFactor();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'SUBSTRING_INDEX(%s, %s, %s)',
            $this->string->dispatch($sqlWalker),
            $this->delimiter->dispatch($sqlWalker),
            $this->count->dispatch($sqlWalker)
        );
    }
}
