<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Sqlite;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

/** @author winkbrace <winkbrace@gmail.com> */
class Round extends FunctionNode
{
    private $firstExpression = null;

    private $secondExpression = null;

    public function parse(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->firstExpression = $parser->SimpleArithmeticExpression();

        // parse second parameter if available
        if ($lexer->lookahead->type === TokenType::T_COMMA) {
            $parser->match(TokenType::T_COMMA);
            $this->secondExpression = $parser->ArithmeticPrimary();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        // use second parameter if parsed
        if ($this->secondExpression !== null) {
            return 'ROUND('
            . $this->firstExpression->dispatch($sqlWalker)
            . ', '
            . $this->secondExpression->dispatch($sqlWalker)
            . ')';
        }

        return 'ROUND(' . $this->firstExpression->dispatch($sqlWalker) . ')';
    }
}
