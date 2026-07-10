<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Mysql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\AST\Node;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;
use function sprintf;
/**
 * @link \https://dev.mysql.com/doc/refman/en/charset-collate.html
 *
 * @author Peter Tanath <peter.tanath@gmail.com>
 */
class Collate extends FunctionNode
{
    /** @var Node|null */
    public $stringPrimary = null;
    /** @var string|null */
    public $collation = null;
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->stringPrimary = $parser->StringPrimary();
        $parser->match(TokenType::T_COMMA);
        $parser->match(TokenType::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $this->collation = $lexer->token->value;
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('%s COLLATE %s', $sqlWalker->walkStringPrimary($this->stringPrimary), $this->collation);
    }
}