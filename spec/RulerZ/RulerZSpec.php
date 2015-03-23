<?php

namespace spec\RulerZ;

use Hoa\Ruler\Model\Model as AST;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use RulerZ\Executor\Executor;
use RulerZ\Interpreter\Interpreter;
use RulerZ\Spec\Specification;

class RulerZSpec extends ObjectBehavior
{
    function let(Interpreter $interpreter)
    {
        $this->beConstructedWith($interpreter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\RulerZ');
    }

    function it_accepts_new_executors_during_construction(Interpreter $interpreter, Executor $executor)
    {
        $this->beConstructedWith($interpreter, [$executor]);
    }

    function it_accepts_new_executors_after_construction(Executor $executor)
    {
        $this->registerExecutor($executor);
    }

    function it_chooses_the_right_executor_for_a_given_target(Interpreter $interpreter, Executor $executor_a, Executor $executor_b, AST $ast)
    {
        $target = $this->getTarget();
        $rule = $this->getRule();

        $interpreter->interpret($rule)->willReturn($ast);

        $executor_a->supports($target, Executor::MODE_FILTER)->willReturn(false);
        $executor_a->filter()->shouldNotBeCalled();

        $executor_b->supports($target, Executor::MODE_FILTER)->willReturn(true);
        $executor_b->filter($target, $ast, [], Argument::type('\RulerZ\Context\ExecutionContext'))->shouldBeCalled();

        $this->beConstructedWith($interpreter, [$executor_a, $executor_b]);

        $this->filter($target, $rule);
    }

    function it_can_filter_a_target_with_a_rule(Interpreter $interpreter, Executor $executor, AST $ast)
    {
        $target = $this->getTarget();
        $result = $this->getResult();
        $rule = $this->getRule();

        $interpreter->interpret($rule)->willReturn($ast);
        $executor->supports($target, Executor::MODE_FILTER)->willReturn(true);
        $executor->filter($target, $ast, [], Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->beConstructedWith($interpreter, [$executor]);

        $this->filter($target, $rule)->shouldReturn($result);
    }

    function it_can_filter_a_target_with_a_specification(Interpreter $interpreter, Executor $executor, AST $ast, Specification $spec)
    {
        $target = $this->getTarget();
        $result = $this->getResult();
        $rule   = $this->getRule();
        $params = [];

        $spec->getRule()->willReturn($rule);
        $spec->getParameters()->willReturn($params);

        $interpreter->interpret($rule)->willReturn($ast);
        $executor->supports($target, Executor::MODE_FILTER)->willReturn(true);
        $executor->filter($target, $ast, $params, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->beConstructedWith($interpreter, [$executor]);

        $this->filterSpec($target, $spec)->shouldReturn($result);
    }

    function it_can_check_if_a_target_satisfies_a_rule(Interpreter $interpreter, Executor $executor, AST $ast)
    {
        $target = $this->getTarget();
        $result = $this->getResult();
        $rule   = $this->getRule();

        $interpreter->interpret($rule)->willReturn($ast);
        $executor->supports($target, Executor::MODE_SATISFIES)->willReturn(true);
        $executor->satisfies($target, $ast, [], Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn(true);

        $this->beConstructedWith($interpreter, [$executor]);

        $this->satisfies($target, $rule)->shouldReturn(true);
    }

    function it_can_check_if_a_target_satisfies_a_specification(Interpreter $interpreter, Executor $executor, AST $ast, Specification $spec)
    {
        $target = $this->getTarget();
        $result = $this->getResult();
        $rule   = $this->getRule();
        $params = [];

        $spec->getRule()->willReturn($rule);
        $spec->getParameters()->willReturn($params);

        $interpreter->interpret($rule)->willReturn($ast);
        $executor->supports($target, Executor::MODE_SATISFIES)->willReturn(true);
        $executor->satisfies($target, $ast, $params, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn(true);

        $this->beConstructedWith($interpreter, [$executor]);

        $this->satisfiesSpec($target, $spec)->shouldReturn(true);
    }

    function it_cant_filter_without_an_executor()
    {
        $this
            ->shouldThrow('RulerZ\Exception\TargetUnsupportedException')
            ->duringFilter(['some target'], 'points > 30');
    }

    private function getTarget()
    {
        return [
            ['name' => 'Joe', 'points' => 40],
            ['name' => 'Moe', 'points' => 20],
        ];
    }

    private function getResult()
    {
        return [
            ['name' => 'Joe', 'points' => 40],
        ];
    }

    private function getRule()
    {
        return 'points > 30';
    }
}
