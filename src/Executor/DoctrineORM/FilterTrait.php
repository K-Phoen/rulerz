<?php

namespace RulerZ\Executor\DoctrineORM;

use RulerZ\Context\ExecutionContext;
use RulerZ\Result\IteratorTools;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function applyFilter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var \Doctrine\ORM\QueryBuilder $target */

        foreach ($this->detectedJoins as $join) {
            $target->join(sprintf('%s.%s', $join['root'], $join['column']), $join['as']);
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
            return $result;
        } else if (is_array($result)) {
            return IteratorTools::fromArray($result);
        }

        throw new \RuntimeException(sprintf('Unhandled result type: "%s"', get_class($result)));
    }
}
