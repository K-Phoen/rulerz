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
        // this will return DQL code
        $dql = $this->execute($target, $operators, $parameters);

        // the root alias can not be determined at compile-time so placeholders are left in the DQL
        $dql = str_replace('@@_ROOT_ALIAS_@@', $target->getRootAliases()[0], $dql);

        // so we apply it to the query builder
        $target->andWhere($dql);

        // now we define the parameters
        foreach ($parameters as $name => $value) {
            $target->setParameter($name, $value);
        }

        // and we return the final results
        return $target->getQuery()->getResult();
    }
}
