<?php

declare(strict_types=1);

namespace RulerZ\Executor\DoctrineORM;

use Doctrine\ORM\QueryBuilder;
use RulerZ\Context\ExecutionContext;
use RulerZ\Result\IteratorTools;
use Doctrine\ORM\Query\Expr;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritdoc}
     */
    public function applyFilter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /* @var \Doctrine\ORM\QueryBuilder $target */

        foreach ($this->detectedJoins as $join) {
            if (!$this->hasExistingIdenticalJoin($target, $join)) {
                $target->leftJoin(sprintf('%s.%s', $join['root'], $join['column']), $join['as']);
            }
        }

        // this will return DQL code
        $dql = $this->execute($target, $operators, $parameters);

        // so we apply it to the query builder
        $target->andWhere($dql);

        // now we define the parameters
        foreach ($parameters as $name => $value) {
            $target->setParameter($name, $value);
        }

        return $target;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /* @var \Doctrine\ORM\QueryBuilder $target */

        $this->applyFilter($target, $parameters, $operators, $context);

        // execute the query
        $result = $target->getQuery()->getResult();

        return IteratorTools::ensureTraversable($result);
    }

    /**
     * @param QueryBuilder $target
     * @param array        $detectedJoin
     *
     * @return bool
     */
    private function hasExistingIdenticalJoin(QueryBuilder $target, array $detectedJoin): bool
    {
        $joins = $target->getDQLPart('join');
        return isset($joins[$detectedJoin['root']]) && count(array_filter(
            $joins[$detectedJoin['root']],
            function (Expr\Join $joinExpr) use ($detectedJoin) {
                return $joinExpr->getAlias() === $detectedJoin['as']
                    && $joinExpr->getJoin() === $detectedJoin['root'].'.'.$detectedJoin['column'];
            }
        )) > 0;
    }
}
