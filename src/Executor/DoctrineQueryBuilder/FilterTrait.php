<?php

namespace RulerZ\Executor\DoctrineQueryBuilder;

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
        /** @var \Doctrine\ORM\QueryBuilder $target */

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

        return $target;
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var \Doctrine\ORM\QueryBuilder $target */

        $this->applyFilter($target, $parameters, $operators, $context);

        // execute the query
        $result = $target->getQuery()->getResult();

        // and return the appropriate result type
        if ($result instanceof \Traversable) {
            return FilterResult::fromTraversable($result);
        } else if (is_array($result)) {
            return FilterResult::fromArray($result);
        }

        throw new \RuntimeException(sprintf('Unhandled result type: "%s"', get_class($result)));
    }
}
