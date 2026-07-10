<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

/** @author Nima S <nimasdj@yahoo.com> */
class FromUnixtime extends FunctionNode
{
    public $firstExpression = null;

    public $secondExpression = null;

    public function getSql(SqlWalker $sqlWalker): string
    {
        if ($this->secondExpression !== null) {
            return 'FROM_UNIXTIME('
                . $this->firstExpression->dispatch($sqlWalker)
                . ','
                . $this->secondExpression->dispatch($sqlWalker)
                . ')';
        }

        return 'FROM_UNIXTIME(' . $this->firstExpression->dispatch($sqlWalker) . ')';
    }

    public function parse(Parser $parser): void
    {
        $lexer = $parser->getLexer();

        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->firstExpression = $parser->ArithmeticPrimary();

        // parse second parameter if available
        if ($lexer->lookahead->type === TokenType::T_COMMA) {
            $parser->match(TokenType::T_COMMA);
            $this->secondExpression = $parser->ArithmeticPrimary();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
