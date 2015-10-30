<?php


class ElasticsearchContext extends BaseContext
{
    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Elasticsearch\Client([
            'hosts' => ['localhost'], // meh.
        ]);
    }

    protected function getCompilationTarget()
    {
        return new \RulerZ\Compiler\Target\Elasticsearch\ElasticsearchVisitor();
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultDataset()
    {
        return $this->client;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultExecutionContext()
    {
        return [
            'index' => 'rulerz_tests',
            'type'  => 'player'
        ];
    }
}
