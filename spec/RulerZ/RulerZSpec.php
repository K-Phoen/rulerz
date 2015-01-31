<?php

namespace spec\RulerZ;

use Hoa\Ruler\Model\Model as AST;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use RulerZ\Executor\Executor;
use RulerZ\Interpreter\Interpreter;

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

        $executor_a->supports($target)->willReturn(false);
        $executor_a->filter()->shouldNotBeCalled();

        $executor_b->supports($target)->willReturn(true);
        $executor_b->filter($target, $ast, [])->shouldBeCalled();

        $this->beConstructedWith($interpreter, [$executor_a, $executor_b]);

        $this->filter($target, $rule);
    }

    function it_can_filter_a_target_with_a_rule(Interpreter $interpreter, Executor $executor, AST $ast)
    {
        $target = $this->getTarget();
        $result = $this->getResult();
        $rule = $this->getRule();

        $interpreter->interpret($rule)->willReturn($ast);
        $executor->supports($target)->willReturn(true);
        $executor->filter($target, $ast, [])->willReturn($result);

        $this->beConstructedWith($interpreter, [$executor]);

        $this->filter($target, $rule)->shouldReturn($result);
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
