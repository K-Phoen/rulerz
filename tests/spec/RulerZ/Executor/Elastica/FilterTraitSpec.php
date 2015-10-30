<?php

namespace spec\RulerZ\Executor\Elastica;

use Elastica\Search;
use Elastica\SearchableInterface;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\ElasticaExecutorStub;

class FilterTraitSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\ElasticaExecutorStub');
    }

    function it_calls_search_on_the_target(Search $target)
    {
        $results = ['result'];
        $esQuery = ['array with the ES query'];

        ElasticaExecutorStub::$executeReturn = $esQuery;
        $target->search(['query' => $esQuery])->willReturn($results);

        $this->filter($target, $parameters = [], $operators = [], new ExecutionContext())->shouldReturn($results);
    }

    function it_works_with_a_searchable_interface(SearchableInterface $target)
    {
        $results = ['result'];
        $esQuery = ['array with the ES query'];

        ElasticaExecutorStub::$executeReturn = $esQuery;
        $target->search(['query' => $esQuery])->willReturn($results);

        $this->filter($target, $parameters = [], $operators = [], new ExecutionContext())->shouldReturn($results);
    }
}