<?php

namespace BeyondSEODeps\DoctrineExtensions\Query\Postgresql;

use BeyondSEODeps\Doctrine\ORM\Query\AST\ASTException;
use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\AST\PathExpression;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\QueryException;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

use function sprintf;

class ExtractFunction extends FunctionNode
{
    /** @var string */
    private $field;

    /** @var PathExpression */
    private $value;

    /** @throws ASTException */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'EXTRACT(%s FROM %s)',
            $this->field,
            $this->value->dispatch($sqlWalker)
        );
    }

    /** @throws QueryException */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $parser->match(TokenType::T_IDENTIFIER);
        $this->field = $parser->getLexer()->token->value;

        $parser->match(TokenType::T_FROM);

        $this->value = $parser->ScalarExpression();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
