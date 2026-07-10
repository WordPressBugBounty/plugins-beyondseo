<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Query\AST;

use BeyondSEODeps\Doctrine\DBAL\Types\Type;

/**
 * Provides an API for resolving the type of a Node
 */
interface TypedExpression
{
    public function getReturnType(): Type;
}
