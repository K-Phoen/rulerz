<?php

namespace RulerZ\Executor\DoctrineDBAL;

use RulerZ\Context\ExecutionContext;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
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

        // and we return the final results
        return $this->returnResult($target, $context);
    }

    private function returnResult($target, ExecutionContext $context)
    {
        if (!empty($context['doctrine_return']) && $context['doctrine_return'] === 'DOCTRINE_QUERY_BUILDER') {
            return $target;
        }

        throw new \RuntimeException('not implemented');
    }
}
