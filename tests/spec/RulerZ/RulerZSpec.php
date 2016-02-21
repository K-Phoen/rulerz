<?php

namespace spec\RulerZ;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use RulerZ\Compiler\Compiler;
use RulerZ\Compiler\Context as CompilationContext;
use RulerZ\Compiler\Target\CompilationTarget;
use RulerZ\Executor\Executor;
use RulerZ\Spec\Specification;

class RulerZSpec extends ObjectBehavior
{
    function let(Compiler $compiler, CompilationTarget $compilationTarget)
    {
        $this->beConstructedWith($compiler, [$compilationTarget]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\RulerZ');
    }

    function it_accepts_new_executors_after_construction(CompilationTarget $anotherCompilationTarget)
    {
        $this->registerCompilationTarget($anotherCompilationTarget);
    }

    function it_chooses_the_right_compilation_target_for_a_given_target(Compiler $compiler, CompilationTarget $compilationTargetYes, CompilationTarget $compilationTargetNo, Executor $executor)
    {
        $target    = ['dummy target'];
        $rule      = 'dummy rule';
        $operators = ['dummy operator'];
        $compilationContext = new CompilationContext();

        $compiler->compile($rule, $compilationTargetYes, $compilationContext)->willReturn($executor);

        $compilationTargetYes->createCompilationContext($target)->willReturn($compilationContext);
        $compilationTargetYes->supports($target, CompilationTarget::MODE_FILTER)->willReturn(true);
        $compilationTargetYes->getOperators()->willReturn($operators);

        $compilationTargetNo->supports($target, CompilationTarget::MODE_FILTER)->willReturn(false);

        $executor->filter($target, [], $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->shouldBeCalled();

        $this->beConstructedWith($compiler, [$compilationTargetNo, $compilationTargetYes]);

        $this->filter($target, $rule);
    }

    function it_can_filter_a_target_with_a_rule(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor)
    {
        $target    = ['dummy target'];
        $rule      = 'dummy rule';
        $operators = ['dummy operator'];
        $result    = 'dummy result';
        $compilationContext = new CompilationContext();

        $compiler->compile($rule, $compilationTarget, $compilationContext)->willReturn($executor);

        $compilationTarget->createCompilationContext($target)->willReturn($compilationContext);
        $compilationTarget->supports($target, CompilationTarget::MODE_FILTER)->willReturn(true);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->filter($target, [], $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->filter($target, $rule)->shouldReturn($result);
    }

    function it_can_filter_a_target_with_a_specification(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor, Specification $spec)
    {
        $target     = ['dummy target'];
        $rule       = 'dummy rule';
        $operators  = ['dummy operator'];
        $result     = 'dummy result';
        $parameters = ['dummy param'];
        $compilationContext = new CompilationContext();

        $spec->getRule()->willReturn($rule);
        $spec->getParameters()->willReturn($parameters);

        $compiler->compile($rule, $compilationTarget, $compilationContext)->willReturn($executor);

        $compilationTarget->createCompilationContext($target)->willReturn($compilationContext);
        $compilationTarget->supports($target, CompilationTarget::MODE_FILTER)->willReturn(true);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->filter($target, $parameters, $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->filterSpec($target, $spec)->shouldReturn($result);
    }

    function it_can_check_if_a_target_satisfies_a_rule(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor)
    {
        $target    = ['dummy target'];
        $rule      = 'dummy rule';
        $operators = ['dummy operator'];
        $result    = true;
        $compilationContext = new CompilationContext();

        $compiler->compile($rule, $compilationTarget, $compilationContext)->willReturn($executor);

        $compilationTarget->createCompilationContext($target)->willReturn($compilationContext);
        $compilationTarget->supports($target, CompilationTarget::MODE_SATISFIES)->willReturn(true);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->satisfies($target, [], $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->satisfies($target, $rule)->shouldReturn($result);
    }

    function it_can_check_if_a_target_satisfies_a_specification(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor, Specification $spec)
    {
        $target     = ['dummy target'];
        $rule       = 'dummy rule';
        $operators  = ['dummy operator'];
        $parameters = ['dummy param'];
        $result    = true;
        $compilationContext = new CompilationContext();

        $spec->getRule()->willReturn($rule);
        $spec->getParameters()->willReturn($parameters);

        $compiler->compile($rule, $compilationTarget, $compilationContext)->willReturn($executor);

        $compilationTarget->createCompilationContext($target)->willReturn($compilationContext);
        $compilationTarget->supports($target, CompilationTarget::MODE_SATISFIES)->willReturn($result);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->satisfies($target, $parameters, $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->satisfiesSpec($target, $spec)->shouldReturn(true);
    }

    function it_cant_filter_without_a_compilation_target()
    {
        $this
            ->shouldThrow('RulerZ\Exception\TargetUnsupportedException')
            ->duringFilter(['some target'], 'points > 30');
    }

    function it_can_apply_a_filter_on_a_target_with_a_rule(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor)
    {
        $target    = ['dummy target'];
        $rule      = 'dummy rule';
        $operators = ['dummy operator'];
        $result    = 'updated target';
        $compilationContext = new CompilationContext();

        $compiler->compile($rule, $compilationTarget, $compilationContext)->willReturn($executor);

        $compilationTarget->createCompilationContext($target)->willReturn($compilationContext);
        $compilationTarget->supports($target, CompilationTarget::MODE_APPLY_FILTER)->willReturn(true);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->applyFilter($target, [], $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->applyFilter($target, $rule)->shouldReturn($result);
    }

    function it_can_apply_a_filter_on_a_target_with_a_specification(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor, Specification $spec)
    {
        $target    = ['dummy target'];
        $rule      = 'dummy rule';
        $operators = ['dummy operator'];
        $result    = 'updated target';
        $compilationContext = new CompilationContext();

        $spec->getRule()->willReturn($rule);
        $spec->getParameters()->willReturn([]);

        $compiler->compile($rule, $compilationTarget, $compilationContext)->willReturn($executor);

        $compilationTarget->createCompilationContext($target)->willReturn($compilationContext);
        $compilationTarget->supports($target, CompilationTarget::MODE_APPLY_FILTER)->willReturn(true);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->applyFilter($target, [], $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->applyFilterSpec($target, $spec)->shouldReturn($result);
    }
}
