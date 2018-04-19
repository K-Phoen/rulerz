<?php

namespace spec\RulerZ\Executor\Pomm;

use PhpSpec\ObjectBehavior;
use PommProject\Foundation\Where;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\PommExecutorStub;
use RulerZ\Stub\ModelStub;
use spec\RulerZ\FilterResultMatcherTrait;

class FilterTraitSpec extends ObjectBehavior
{
    use FilterResultMatcherTrait;

    public function let()
    {
        $this->beAnInstanceOf(PommExecutorStub::class);
    }

    public function it_can_apply_a_filter_on_a_target(ModelStub $modelStub)
    {
        $whereClause = new Where();

        PommExecutorStub::$executeReturn = $whereClause;
        $modelStub->findWhere($whereClause)->shouldNotBeCalled();

        $this->applyFilter($modelStub, $parameters = [], $operators = [], new ExecutionContext())->shouldReturn($whereClause);
    }

    public function it_call_findWhere_on_the_target(ModelStub $modelStub, Where $whereClause)
    {
        $results = ['result'];

        PommExecutorStub::$executeReturn = $whereClause;
        $modelStub->findWhere($whereClause)->willReturn($results);

        $this->filter($modelStub, $parameters = [], $operators = [], new ExecutionContext())->shouldReturnResults($results);
    }

    public function it_call_acustom_method_if_specified_in_the_context(ModelStub $modelStub, Where $whereClause)
    {
        $results = ['result'];

        PommExecutorStub::$executeReturn = $whereClause;
        $modelStub->findCustom($whereClause)->willReturn($results);

        $this->filter($modelStub, $parameters = [], $operators = [], new ExecutionContext([
            'method' => 'findCustom',
        ]))->shouldReturnResults($results);
    }
}
