<?php

declare(strict_types=1);

namespace RulerZ\Executor\DoctrineORM;

use RulerZ\Context\ExecutionContext;
use RulerZ\Result\IteratorTools;

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
            $target->leftJoin(sprintf('%s.%s', $join['root'], $join['column']), $join['as']);
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
}
