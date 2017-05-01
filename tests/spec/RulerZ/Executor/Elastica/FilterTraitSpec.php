<?php

namespace spec\RulerZ\Executor\Elastica;

use Elastica\Search;
use Elastica\SearchableInterface;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\ElasticaExecutorStub;
use spec\RulerZ\FilterResultMatcherTrait;

class FilterTraitSpec extends ObjectBehavior
{
    use FilterResultMatcherTrait;

    public function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\ElasticaExecutorStub');
    }

    public function it_can_apply_a_filter_on_a_target(Search $target)
    {
        $esQuery = ['array with the ES query'];

        ElasticaExecutorStub::$executeReturn = $esQuery;
        $target->search(['query' => $esQuery])->shouldNotBeCalled();

        $this->applyFilter($target, $parameters = [], $operators = [], new ExecutionContext())->shouldReturn($esQuery);
    }

    public function it_calls_search_on_the_target(Search $target)
    {
        $results = new \ArrayIterator(['result']);
        $esQuery = ['array with the ES query'];

        ElasticaExecutorStub::$executeReturn = $esQuery;
        $target->search(['query' => $esQuery])->willReturn($results);

        $this->filter($target, $parameters = [], $operators = [], new ExecutionContext())->shouldReturnResults($results);
    }

    public function it_works_with_a_searchable_interface(SearchableInterface $target)
    {
        $results = new \ArrayIterator(['result']);
        $esQuery = ['array with the ES query'];

        ElasticaExecutorStub::$executeReturn = $esQuery;
        $target->search(['query' => $esQuery])->willReturn($results);

        $this->filter($target, $parameters = [], $operators = [], new ExecutionContext())->shouldReturnResults($results);
    }
}
