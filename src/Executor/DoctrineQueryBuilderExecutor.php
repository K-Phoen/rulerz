<?php

namespace RulerZ\Executor;

use Doctrine\ORM\QueryBuilder;
use Hoa\Ruler\Model;

use RulerZ\Visitor\DoctrineQueryBuilderVisitor;

/**
 * Execute a rule on a query builder.
 */
class DoctrineQueryBuilderExecutor implements Executor
{
    /**
     * @var array A list of additionnal operators.
     */
    private $operators = [];

    public function __construct(array $operators = [])
    {
        $this->registerOperators($operators);
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, Model $rule, array $parameters = [])
    {
        $target = clone $target;
        $target->andWhere($this->buildWhereClause($target, $rule));

        foreach ($parameters as $name => $value) {
            $target->setParameter($name, $value);
        }

        return $target->getQuery()->getResult();
    }

    /**
     * {@inheritDoc}
     */
    public function satisfies($target, Model $rule, array $parameters = [])
    {
        return count($this->filter($target, $rule, $parameters)) !== 0;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function registerOperators(array $operators)
    {
        $this->operators = array_merge($this->operators, $operators);
    }

    /**
     * Builds the DQL code for the given rule.
     *
     * @param mixed $target The target to filter.
     * @param Model $rule   The rule to apply.
     *
     * @return string The DQL code.
     */
    private function buildWhereClause($target, Model $rule)
    {
        $dqlBuilder = new DoctrineQueryBuilderVisitor($target);

        foreach ($this->operators as $name => $callable) {
            $dqlBuilder->setOperator($name, $callable);
        }

        return $dqlBuilder->visit($rule);
    }
}
