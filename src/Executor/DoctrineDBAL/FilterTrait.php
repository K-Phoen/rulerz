<?php

namespace RulerZ\Executor\DoctrineDBAL;

use RulerZ\Context\ExecutionContext;
use RulerZ\Result\FilterResult;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function applyFilter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $target */

        // this will return DQL code
        $sql = $this->execute($target, $operators, $parameters);

        // so we apply it to the query builder
        $target->andWhere($sql);

        // now we define the parameters
        foreach ($parameters as $name => $value) {
            $target->setParameter($name, $value);
        }

        return $target;
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $target */

        $this->applyFilter($target, $parameters, $operators, $context);

        // and return the results
        return FilterResult::fromArray($target->execute()->fetchAll());
    }
}
