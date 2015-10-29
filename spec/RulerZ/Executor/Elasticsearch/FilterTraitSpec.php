<?php

namespace spec\RulerZ\Executor\Elasticsearch;

use Elasticsearch\Client;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\ElasticsearchExecutorStub;

class FilterTraitSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\ElasticsearchExecutorStub');
    }

    function it_calls_search_on_the_target(Client $target)
    {
        $documents = [
            'first document',
            'other document',
        ];
        $result = [
            '_shards' => [],
            'hits' => [
                'total' => 1,
                'hits'  => [
                    ['_source' => 'first document'],
                    ['_source' => 'other document' ],
                ],
            ]
        ];
        $esQuery = ['array with the ES query'];

        ElasticsearchExecutorStub::$executeReturn = $esQuery;
        $target->search([
            'index' => 'es_index',
            'type'  => 'es_type',
            'body'  => ['query' => $esQuery],
        ])->willReturn($result);

        $this->filter($target, $parameters = [], $operators = [], new ExecutionContext([
            'index' => 'es_index',
            'type'  => 'es_type',
        ]))->shouldReturn($documents);
    }

    function it_throws_an_exception_when_the_execution_context_is_incomplete(Client $target)
    {
        $this
            ->shouldThrow('RuntimeException')
            ->duringFilter($target, $parameters = [], $operators = [], new ExecutionContext());
    }
}
