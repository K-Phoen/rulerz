<?php

class ElasticsearchContext extends BaseContext
{
    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->client = new Elasticsearch\Client([
            'hosts' => [
                sprintf('%s:%d', $_ENV['ELASTICSEARCH_HOST'], $_ENV['ELASTICSEARCH_PORT']),
            ],
        ]);
    }

    protected function getCompilationTarget()
    {
        return new \RulerZ\Target\Elasticsearch\Elasticsearch();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultDataset()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultExecutionContext()
    {
        return [
            'index' => 'rulerz_tests',
            'type' => 'player',
        ];
    }
}
