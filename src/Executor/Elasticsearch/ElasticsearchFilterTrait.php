<?php

namespace RulerZ\Executor\Elasticsearch;

use RulerZ\Context\ExecutionContext;

trait ElasticsearchFilterTrait
{
    // just because traits can not have constants
    private static $DEFAULT_CHUNK_SIZE = 50;
    private static $DEFAULT_SCROLL_DURATION = '30s';

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

        /** @var \Elasticsearch\Client $target */
        $response = $target->search([
            'index' => $context['index'],
            'type' => $context['type'],
            'search_type' => 'scan',
            'scroll' => $context->get('scroll_duration', self::$DEFAULT_SCROLL_DURATION),
            'size' => $context->get('chunks_size', self::$DEFAULT_CHUNK_SIZE),
            'body' => ['query' => $searchQuery],
        ]);

        $scrollId = $response['_scroll_id'];

        while (true) {
            $results = $target->scroll([
                'scroll_id' => $scrollId,
                'scroll' => $context->get('scroll_duration', self::$DEFAULT_SCROLL_DURATION),
            ]);

            if (empty($results['hits']['hits'])) {
                break;
            }

            $scrollId = $results['_scroll_id'];

            foreach ($results['hits']['hits'] as $result) {
                yield  $result['_source'];
            }
        }
    }
}
