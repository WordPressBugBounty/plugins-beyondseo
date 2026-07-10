<?php

declare(strict_types=1);

namespace BeyondSEODeps\Doctrine\ORM\Tools\Pagination;

use BeyondSEODeps\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use BeyondSEODeps\Doctrine\ORM\Query\AST\OrderByClause;
use BeyondSEODeps\Doctrine\ORM\Query\Parser;
use BeyondSEODeps\Doctrine\ORM\Query\SqlWalker;
use BeyondSEODeps\Doctrine\ORM\Tools\Pagination\Exception\RowNumberOverFunctionNotEnabled;

use function trim;

/**
 * RowNumberOverFunction
 *
 * Provides ROW_NUMBER() OVER(ORDER BY...) construct for use in LimitSubqueryOutputWalker
 */
class RowNumberOverFunction extends FunctionNode
{
    /** @var OrderByClause */
    public $orderByClause;

    /** @inheritDoc */
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'ROW_NUMBER() OVER(' . trim($sqlWalker->walkOrderByClause(
            $this->orderByClause
        )) . ')';
    }

    /**
     * @throws RowNumberOverFunctionNotEnabled
     *
     * @inheritDoc
     */
    public function parse(Parser $parser)
    {
        throw RowNumberOverFunctionNotEnabled::create();
    }
}
