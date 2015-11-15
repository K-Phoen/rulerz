<?php

namespace RulerZ\Executor\Solr;

use RulerZ\Context\ExecutionContext;

trait SolariumFilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var \Solarium\Client $target */

        /** @var string $searchQuery */
        $searchQuery = $this->execute($target, $operators, $parameters);

        $query = $target->createSelect();
        $query->createFilterQuery('rulerz')->setQuery($searchQuery);

        return $target->select($query)->getIterator();
    }
}
