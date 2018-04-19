<?php

namespace spec\RulerZ\Executor\PHP;

use PhpSpec\ObjectBehavior;
use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\ArrayExecutorStub;
use spec\RulerZ\FilterResultMatcherTrait;

class FilterTraitSpec extends ObjectBehavior
{
    use FilterResultMatcherTrait;

    public function let()
    {
        $this->beAnInstanceOf(ArrayExecutorStub::class);
    }

    public function it_can_NOT_apply_a_filter_on_a_target()
    {
        $target = [['some' => 'item'], ['another' => 'item']];

        $this
            ->shouldThrow(\LogicException::class)
            ->duringApplyFilter($target, $parameters = [], $operators = [], new ExecutionContext());
    }

    public function it_filters_the_target_using_execute()
    {
        $target = [['some' => 'item'], ['another' => 'item']];
        $results = $target;

        ArrayExecutorStub::$executeReturn = true;

        $this->filter($target, $parameters = [], $operators = [], new ExecutionContext())->shouldReturnResults($results);
    }
}
