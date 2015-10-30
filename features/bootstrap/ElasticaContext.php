<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class ElasticaContext extends BaseContext
{
    /**
     * @var \Elastica\Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new \Elastica\Client([
            'host' => 'localhost', // meh.
        ]);
    }

    protected function getCompilationTarget()
    {
        return new \RulerZ\Compiler\Target\Elasticsearch\ElasticaVisitor();
    }

    protected function getDefaultDataset()
    {
        $search = new \Elastica\Search($this->client);
        $search
            ->addIndex('rulerz_tests')
            ->addType('player');

        return $search;
    }
}
