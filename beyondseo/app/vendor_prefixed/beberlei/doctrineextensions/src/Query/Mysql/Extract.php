<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\QueryException;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

use function in_array;
use function strtoupper;

/** @author Ahwalian Masykur <ahwalian@gmail.com> */
class Extract extends DateAdd
{
    public $date = null;

    public $unit = null;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $parser->match(TokenType::T_IDENTIFIER);
        $lexer      = $parser->getLexer();
        $this->unit = $lexer->token->value;

        $parser->match(TokenType::T_IDENTIFIER);
        $this->date = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $unit = strtoupper($this->unit);
        if (! in_array($unit, self::$allowedUnits)) {
            throw QueryException::semanticalError('EXTRACT() does not support unit "' . $unit . '".');
        }

        return 'EXTRACT(' . $unit . ' FROM ' . $this->date->dispatch($sqlWalker) . ')';
    }
}
