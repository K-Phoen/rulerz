<?php

namespace RulerZ\Executor;

use Doctrine\ORM\QueryBuilder;
use Hoa\Ruler\Model;

use RulerZ\Context\ExecutionContext;
use RulerZ\Visitor\DoctrineQueryBuilderVisitor;

/**
 * Execute a rule on a query builder.
 */
class DoctrineQueryBuilderExecutor implements ExtendableExecutor
{
    use Polyfill\ExtendableExecutor;
    use Polyfill\FilterBasedSatisfaction;

    /**
     * Constructs the Doctrine QueryBuilder executor.
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
        $target->andWhere($this->buildWhereClause($target, $rule));

        foreach ($parameters as $name => $value) {
            $target->setParameter($name, $value);
        }

        return $target->getQuery()->getResult();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder;
    }

    /**
     * Builds the DQL code for the given rule.
     *
     * @param QueryBuilder $qb   The QueryBuilder to filter.
     * @param Model        $rule The rule to apply.
     *
     * @return string The DQL code.
     */
    private function buildWhereClause(QueryBuilder $qb, Model $rule)
    {
        $dqlBuilder = new DoctrineQueryBuilderVisitor($qb);
        $dqlBuilder->setOperators($this->getOperators());

        $dql = $dqlBuilder->visit($rule);
        var_dump($dql);
        return $dql;
    }
}
