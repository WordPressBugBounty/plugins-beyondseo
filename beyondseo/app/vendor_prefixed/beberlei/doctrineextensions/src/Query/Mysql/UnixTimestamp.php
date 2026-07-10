<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

use function sprintf;

/**
 * @author      Rafael Kassner <kassner@gmail.com>
 * @author      Oleg Khussainov <getmequick@gmail.com>
 */
class UnixTimestamp extends FunctionNode
{
    public $date;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'UNIX_TIMESTAMP(%s)',
            $this->date ? $sqlWalker->walkArithmeticPrimary($this->date) : ''
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        if (! $parser->getLexer()->isNextToken(TokenType::T_CLOSE_PARENTHESIS)) {
            $this->date = $parser->ArithmeticPrimary();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
