<?php

namespace spec\RulerZ\Executor\Elasticsearch;

use Elasticsearch\Client;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\ElasticsearchExecutorStub;
use spec\RulerZ\FilterResultMatcherTrait;

class FilterTraitSpec extends ObjectBehavior
{
    use FilterResultMatcherTrait;

    function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\ElasticsearchExecutorStub');
    }

    function it_can_apply_a_filter_on_a_target(Client $target)
    {
        $results = new \ArrayIterator(['result']);
        $esQuery = ['array with the ES query'];

        ElasticsearchExecutorStub::$executeReturn = $esQuery;
        $target->search()->shouldNotBeCalled();

        $this->applyFilter($target, $parameters = [], $operators = [], new ExecutionContext())->shouldReturn($esQuery);
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
        ]))->shouldReturnResults($documents);
    }

    function it_throws_an_exception_when_the_execution_context_is_incomplete(Client $target)
    {
        $this
            ->shouldThrow('RuntimeException')
            ->duringFilter($target, $parameters = [], $operators = [], new ExecutionContext());
    }
}
