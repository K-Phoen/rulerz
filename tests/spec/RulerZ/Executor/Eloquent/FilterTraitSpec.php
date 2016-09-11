<?php

namespace spec\RulerZ\Executor\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection as EloquentCollection;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\EloquentExecutorStub;
use spec\RulerZ\FilterResultMatcherTrait;

class FilterTraitSpec extends ObjectBehavior
{
    use FilterResultMatcherTrait;

    function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\EloquentExecutorStub');
    }

    function it_can_apply_a_filter_on_a_target(QueryBuilder $queryBuilder)
    {
        $parameters = [];
        $sql        = 'sql query';

        EloquentExecutorStub::$executeReturn = $sql;
        $queryBuilder->whereRaw($sql, $parameters)->shouldBeCalled();
        $queryBuilder->get()->shouldNotBeCalled();

        $this->applyFilter($queryBuilder, $parameters, $operators = [], new ExecutionContext())->shouldReturn($queryBuilder);
    }

    function it_handles_query_builders(QueryBuilder $queryBuilder)
    {
        $results    = new EloquentCollection(['result']);
        $parameters = [];
        $sql        = 'sql query';

        EloquentExecutorStub::$executeReturn = $sql;
        $queryBuilder->whereRaw($sql, $parameters)->shouldBeCalled();
        $queryBuilder->get()->willReturn($results);

        $this->filter($queryBuilder, $parameters, $operators = [], new ExecutionContext())->shouldReturnResults($results);
    }

    function it_handles_eloquent_builders(EloquentBuilder $eloquentBuilder, QueryBuilder $builder)
    {
        $results    = new EloquentCollection(['result']);
        $parameters = [];
        $sql        = 'sql query';

        EloquentExecutorStub::$executeReturn = $sql;
        $eloquentBuilder->getQuery()->willReturn($builder);
        $builder->whereRaw($sql, $parameters)->shouldBeCalled();
        $builder->get()->willReturn($results);

        $this->filter($eloquentBuilder, $parameters, $operators = [], new ExecutionContext())->shouldReturnResults($results);
    }
}
