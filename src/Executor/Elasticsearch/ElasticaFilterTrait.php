<?php

declare(strict_types=1);

namespace RulerZ\Executor\Elasticsearch;

use Elastica\Search;
use Elastica\SearchableInterface;

use RulerZ\Context\ExecutionContext;

trait ElasticaFilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritdoc}
     */
    public function applyFilter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        return $this->execute($target, $operators, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var array $searchQuery */
        $searchQuery = $this->execute($target, $operators, $parameters);

        if ($target instanceof SearchableInterface || $target instanceof Search) {
            return $target->search(['query' => $searchQuery]);
        }

        return $target->find(['query' => $searchQuery]);
    }
}
