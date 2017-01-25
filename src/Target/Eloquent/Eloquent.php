<?php

namespace RulerZ\Target\Eloquent;

use RulerZ\Compiler\Context;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

use RulerZ\Target\AbstractSqlTarget;

class Eloquent extends AbstractSqlTarget
{
    /**
     * Allow eloquent builder as query.
     *
     * @var bool
     */
    protected $allowEloquentBuilderAsQuery = false;

    /**
     * @param bool $allowEloquentBuilderAsQuery Whether to allow the execution target to be eloquent builder instead of query builder.
     */
    public function __construct($allowEloquentBuilderAsQuery = false)
    {
        parent::__construct();

        $this->allowEloquentBuilderAsQuery = (bool) $allowEloquentBuilderAsQuery;
    }

    /**
     * @inheritDoc
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder || $target instanceof EloquentBuilder;
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Eloquent\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function createVisitor(Context $context)
    {
        return new EloquentVisitor($context, $this->getOperators(), $this->allowStarOperator, $this->allowEloquentBuilderAsQuery);
    }
}
