<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Query\AST\Functions;

use BeyondSEODeps\Doctrine\DBAL\Types\Type;
use BeyondSEODeps\Doctrine\DBAL\Types\Types;
use BeyondSEODeps\Doctrine\ORM\Query\AST\Node;
use BeyondSEODeps\Doctrine\ORM\Query\AST\TypedExpression;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

/**
 * "LENGTH" "(" StringPrimary ")"
 *
 * @link    www.doctrine-project.org
 */
class LengthFunction extends FunctionNode implements TypedExpression
{
    /** @var Node */
    public $stringPrimary;

    /** @inheritDoc */
    public function getSql(SqlWalker $sqlWalker)
    {
        return $sqlWalker->getConnection()->getDatabasePlatform()->getLengthExpression(
            $sqlWalker->walkSimpleArithmeticExpression($this->stringPrimary)
        );
    }

    /** @inheritDoc */
    public function parse(Parser $parser)
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->stringPrimary = $parser->StringPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getReturnType(): Type
    {
        return Type::getType(Types::INTEGER);
    }
}
