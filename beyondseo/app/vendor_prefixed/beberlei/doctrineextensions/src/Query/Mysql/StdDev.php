<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

use function sprintf;

/** @author Joachim Schirrmacher <j.schirrmacher@dilab.co> */
class StdDev extends FunctionNode
{
    public $arithmeticExpression;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('STDDEV(%s)', $sqlWalker->walkSimpleArithmeticExpression($this->arithmeticExpression));
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->arithmeticExpression = $parser->SimpleArithmeticExpression();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
