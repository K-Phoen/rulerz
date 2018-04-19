<?php

declare(strict_types=1);

namespace spec\RulerZ\Executor\Elasticsearch;

use Elasticsearch\Client;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\ElasticsearchExecutorStub;
use spec\RulerZ\FilterResultMatcherTrait;

class FilterTraitSpec extends ObjectBehavior
{
    use FilterResultMatcherTrait;

    public function let()
    {
        $this->beAnInstanceOf(ElasticsearchExecutorStub::class);
    }

    public function it_can_apply_a_filter_on_a_target(Client $target)
    {
        $esQuery = ['array with the ES query'];

        ElasticsearchExecutorStub::$executeReturn = $esQuery;
        $target->search()->shouldNotBeCalled();

        $this->applyFilter($target, $parameters = [], $operators = [], new ExecutionContext())->shouldReturn($esQuery);
    }

    public function it_calls_search_on_the_target(Client $target)
    {
        $documents = [
            'first document',
            'other document',
        ];
        $result = [
            '_scroll_id' => 'some-scroll-id',
        ];
        $esQuery = ['array with the ES query'];

        ElasticsearchExecutorStub::$executeReturn = $esQuery;
        $target->search([
            'index' => 'es_index',
            'type' => 'es_type',
            'search_type' => 'scan',
            'scroll' => '30s',
            'size' => 50,
            'body' => ['query' => $esQuery],
        ])->willReturn($result);

        $target->scroll([
            'scroll_id' => 'some-scroll-id',
            'scroll' => '30s',
        ])->willReturn([
            '_scroll_id' => 'some-scroll-id',
            'hits' => [
                'total' => 1,
                'hits' => [
                    ['_source' => 'first document'],
                    ['_source' => 'other document'],
                ],
            ],
        ], [
            '_scroll_id' => 'some-scroll-id',
            'hits' => [
                'total' => 1,
                'hits' => [],
            ],
        ]);

        $this->filter($target, $parameters = [], $operators = [], new ExecutionContext([
            'index' => 'es_index',
            'type' => 'es_type',
        ]))->shouldReturnResults($documents);
    }
}
