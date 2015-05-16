<?php

namespace RulerZ\Executor;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Hoa\Ruler\Model;

use RulerZ\Context\ExecutionContext;
use RulerZ\Visitor\EloquentQueryBuilderVisitor;

/**
 * Execute a rule on an Eloquent query builder.
 */
class EloquentExecutor implements ExtendableExecutor
{
    use Polyfill\ExtendableExecutor;
    use Polyfill\FilterBasedSatisfaction;

    /**
     * Constructs the Eloquent query builder executor.
     *
     * @param array $operators A list of custom operators to register.
     */
    public function __construct(array $operators = [])
    {
        $this->registerOperators($operators);
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, Model $rule, array $parameters, ExecutionContext $context)
    {
        $query = !$target instanceof QueryBuilder ? $target->getQuery() : $target;

        $query->whereRaw($this->buildWhereClause($query, $rule), $parameters);

        return $query->get();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder || $target instanceof EloquentBuilder;
    }

    /**
     * Builds the SQL code for the given rule.
     *
     * @param QueryBuilder $qb   The QueryBuilder to filter.
     * @param Model        $rule The rule to apply.
     *
     * @return string The SQL code.
     */
    private function buildWhereClause(QueryBuilder $qb, Model $rule)
    {
        $searchBuilder = new EloquentQueryBuilderVisitor();
        $searchBuilder->setOperators($this->getOperators());

        return $searchBuilder->visit($rule);
    }
}
