<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Query\AST\Functions;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Node;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Query\TokenType;

use function sprintf;

/**
 * "LOWER" "(" StringPrimary ")"
 *
 * @link    www.doctrine-project.org
 */
class LowerFunction extends FunctionNode
{
    /** @var Node */
    public $stringPrimary;

    /** @inheritDoc */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'LOWER(%s)',
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
}
