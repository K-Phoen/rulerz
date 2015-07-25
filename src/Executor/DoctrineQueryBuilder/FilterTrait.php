<?php

namespace RulerZ\Executor\DoctrineQueryBuilder;

use RulerZ\Context\ExecutionContext;
use RulerZ\Context\ObjectContext;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        // this will update the query builder
        $this->execute($target, $operators, $parameters);

        // now we define the parameters
        foreach ($parameters as $name => $value) {
            $target->setParameter($name, $value);
        }

        // and we return the final results
        return $target->getQuery()->getResult();
    }
}
