<?php

namespace RulerZ\Executor\Elasticsearch;

use RulerZ\Context\ExecutionContext;
use RulerZ\Result\IteratorTools;

trait ElasticsearchFilterTrait
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
        $chunkSize = 100;
        $searchParams = [
            'index' => $context['index'],
            'type' => $context['type'],
            'body' => [
                'from' => 0,
                'size' => $chunkSize,
                'query' => $searchQuery,
            ],
        ];

        /** @var \Elasticsearch\Client $target */
        $results = $target->search($searchParams);

        if (empty($results['hits'])) {
            return IteratorTools::fromArray([]);
        }

        $totalHits = $results['hits']['total'];
        if ($totalHits > count($results['hits']['hits'])) {
            return IteratorTools::fromGenerator(
                function () use ($results, $totalHits, $chunkSize, $target, $searchParams) {
                    foreach ($results['hits']['hits'] as $result) {
                        yield $result['_source'];
                    }

                    for ($i = $chunkSize; $i < $totalHits; $i += $chunkSize) {
                        $searchParams['body']['from'] = $i;
                        $results = $target->search($searchParams);
                        if (empty($results['hits']['hits'])) {
                            return;
                        }

                        foreach ($results['hits']['hits'] as $result) {
                            yield $result['_source'];
                        }
                    }
                }
            );
        }

        return IteratorTools::fromArray(array_column($results['hits']['hits'], '_source'));
    }
}
