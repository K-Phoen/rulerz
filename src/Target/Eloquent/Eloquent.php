<?php

declare(strict_types=1);

namespace RulerZ\Target\Eloquent;

use RulerZ\Compiler\Context;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

use RulerZ\Executor\Eloquent\FilterTrait;
use RulerZ\Executor\Polyfill\FilterBasedSatisfaction;
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
    public function __construct(bool $allowEloquentBuilderAsQuery = false)
    {
        parent::__construct();

        $this->allowEloquentBuilderAsQuery = $allowEloquentBuilderAsQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($target, string $mode): bool
    {
        return $target instanceof QueryBuilder || $target instanceof EloquentBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutorTraits()
    {
        return [
            FilterTrait::class,
            FilterBasedSatisfaction::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function createVisitor(Context $context)
    {
        return new EloquentVisitor($context, $this->getOperators(), $this->allowStarOperator, $this->allowEloquentBuilderAsQuery);
    }
}
