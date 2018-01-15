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
     * Preserve positional parameters.
     *
     * @var bool
     */
    protected $preservePositionalParameters = false;

    /**
     * @param bool $allowEloquentBuilderAsQuery Whether to allow the execution target to be eloquent builder instead of query builder.
     */
    public function __construct(
        $allowEloquentBuilderAsQuery = false,
        $preservePositionalParameters = false
    ) {
        parent::__construct();

        $this->allowEloquentBuilderAsQuery = (bool) $allowEloquentBuilderAsQuery;
        $this->preservePositionalParameters = (bool) $preservePositionalParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder || $target instanceof EloquentBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Eloquent\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function createVisitor(Context $context)
    {
        return new EloquentVisitor(
            $context,
            $this->getOperators(),
            $this->allowStarOperator,
            $this->allowEloquentBuilderAsQuery,
            $this->preservePositionalParameters
        );
    }
}
