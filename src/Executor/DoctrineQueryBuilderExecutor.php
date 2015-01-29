<?php
namespace Executor;

use Doctrine\ORM\QueryBuilder;
use Hoa\Ruler\Model;

use Visitor\DoctrineQueryBuilderVisitor;

/**
 * Execute a rule on a query builder.
 */
class DoctrineQueryBuilderExecutor implements Executor
{
    /**
     * {@inheritDoc}
     */
    public function filter($target, Model $rule, array $parameters = [])
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
    public function supports($target)
    {
        return $target instanceof QueryBuilder;
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

        return $dqlBuilder->visit($rule);
    }
}
