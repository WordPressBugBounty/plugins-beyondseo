<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;
/**
 * "FROM_BASE64" "(" "$fieldIdentifierExpression" ")"
 *
 * @link \https://dev.mysql.com/doc/refman/en/string-functions.html#function_from-base64
 *
 * @example SELECT FROM_BASE64(foo) FROM dual;
 */
class FromBase64 extends FunctionNode
{
    public $field = null;
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->field = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'FROM_BASE64(' . $this->field->dispatch($sqlWalker) . ')';
    }
}