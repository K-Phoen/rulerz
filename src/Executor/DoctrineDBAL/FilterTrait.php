<?php

namespace RulerZ\Executor\DoctrineDBAL;

use Doctrine\DBAL\Connection;
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
        /** @var \Doctrine\DBAL\Query\QueryBuilder $target */

        // this will return DQL code
        $sql = $this->execute($target, $operators, $parameters);

        // so we apply it to the query builder
        $target->andWhere($sql);

        // now we define the parameters
        foreach ($parameters as $name => $value) {
            $target->setParameter($name, $value, is_array($value) ? Connection::PARAM_STR_ARRAY : null);
        }

        return $target;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /* @var \Doctrine\DBAL\Query\QueryBuilder $target */

        $this->applyFilter($target, $parameters, $operators, $context);

        // and return the results
        return IteratorTools::fromArray($target->execute()->fetchAll());
    }
}
