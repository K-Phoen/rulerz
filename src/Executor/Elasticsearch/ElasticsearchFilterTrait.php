<?php

namespace RulerZ\Executor\Elasticsearch;

use RulerZ\Context\ExecutionContext;
use RulerZ\Result\FilterResult;

trait ElasticsearchFilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var array $searchQuery */
        $searchQuery = $this->execute($target, $operators, $parameters);

        /** @var \Elasticsearch\Client $target */
        $results = $target->search([
            'index' => $context['index'],
            'type'  => $context['type'],
            'body'  => ['query' => $searchQuery],
        ]);

        if (empty($results['hits'])) {
            return FilterResult::fromArray([]);
        }

        return FilterResult::fromArray(array_map(function($result) {
            return $result['_source'];
        }, $results['hits']['hits']));
    }
}
