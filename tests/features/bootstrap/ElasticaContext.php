<?php

class ElasticaContext extends BaseContext
{
    /**
     * @var \Elastica\Client
     */
    private $client;

    /**
     * {@inheritDoc}
     */
    protected function initialize()
    {
        $this->client = new \Elastica\Client([
            'host' => $_ENV['ELASTICSEARCH_HOST'],
            'port' => $_ENV['ELASTICSEARCH_PORT'],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getCompilationTarget()
    {
        return new \RulerZ\Compiler\Target\Elasticsearch\ElasticaVisitor();
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultDataset()
    {
        $search = new \Elastica\Search($this->client);
        $search
            ->addIndex('rulerz_tests')
            ->addType('player');

        return $search;
    }
}
