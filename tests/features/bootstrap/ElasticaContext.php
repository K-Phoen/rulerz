<?php

declare(strict_types=1);

class ElasticaContext extends BaseContext
{
    /**
     * @var \Elastica\Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->client = new \Elastica\Client([
            'host' => $_ENV['ELASTICSEARCH_HOST'],
            'port' => $_ENV['ELASTICSEARCH_PORT'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompilationTarget()
    {
        return new \RulerZ\Target\Elastica\Elastica();
    }

    /**
     * {@inheritdoc}
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
