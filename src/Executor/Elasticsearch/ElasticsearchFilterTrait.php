<?php

namespace RulerZ\Executor\Elasticsearch;

use RulerZ\Context\ExecutionContext;

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

        return array_map(function($result) {
            return $result['_source'];
        }, $results['hits']['hits']);
    }
}
