<?php

namespace RulerZ\Visitor;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Hoa\Ruler\Model as AST;

use RulerZ\Model;

class EloquentQueryBuilderVisitor extends SqlVisitor
{
    /**
     * The QueryBuilder to update.
     *
     * @var QueryBuilder
     */
    private $qb;

    /**
     * Constructor.
     *
     * @param QueryBuilder $qb                The query builder being manipulated.
     * @param bool         $allowStarOperator Whether to allow the star operator or not (ie: implicit support of unknown operators).
     */
    public function __construct(QueryBuilder $qb, $allowStarOperator = true)
    {
        parent::__construct($allowStarOperator);

        $this->qb = $qb;
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // make it a placeholder
        return ':'.$element->getName();
    }
}
