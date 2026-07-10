<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Oracle;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\AST\Node;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

use function sprintf;

/** @author Mohammad ZeinEddin <mohammad@zeineddin.name> */
class Trunc extends FunctionNode
{
    /** @var Node */
    private $date;

    /** @var Node */
    private $fmt;

    public function getSql(SqlWalker $sqlWalker): string
    {
        if ($this->fmt) {
            return sprintf(
                'TRUNC(%s, %s)',
                $sqlWalker->walkArithmeticPrimary($this->date),
                $sqlWalker->walkArithmeticPrimary($this->fmt)
            );
        }

        return sprintf(
            'TRUNC(%s)',
            $sqlWalker->walkArithmeticPrimary($this->date)
        );
    }

    public function parse(Parser $parser): void
    {
        $lexer = $parser->getLexer();

        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->date = $parser->ArithmeticExpression();

        if ($lexer->isNextToken(TokenType::T_COMMA)) {
            $parser->match(TokenType::T_COMMA);
            $this->fmt = $parser->StringExpression();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
