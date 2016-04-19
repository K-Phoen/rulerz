<?php

namespace spec\RulerZ\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Context as CompilationContext;
use RulerZ\Compiler\EvalEvaluator;

class CompilerSpec extends ObjectBehavior
{
    function let(EvalEvaluator $evaluator)
    {
        $this->beConstructedWith($evaluator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Compiler\Compiler');
    }

    function it_delegates_code_evaluation_when_the_executor_is_not_loaded(EvalEvaluator $evaluator, CompilationTarget $target)
    {
        $rule = 'points > 42';
        $context = new CompilationContext();
        $self = $this;
        $expectedExecutorName = null;

        $evaluator->evaluate(Argument::any(), Argument::any())->will(function($args) use ($self, &$expectedExecutorName) {
            $expectedExecutorName = 'Executor_'.$args[0];

            $self->loadExecutor($expectedExecutorName);
        });

        // the compiler returns an instance of the compiled Executor
        $executor = $this->compile($rule, $target, $context);
        $executor->shouldHaveType('RulerZ\Compiled\Executor\\'.$expectedExecutorName);

        // and calling the compiler again does not fail
        $executor = $this->compile($rule, $target, $context);
        $executor->shouldHaveType('RulerZ\Compiled\Executor\\'.$expectedExecutorName);
    }

    private function loadExecutor($classnName)
    {
        $source = <<<EXECUTOR
namespace RulerZ\Compiled\Executor;
        
class $classnName {}
EXECUTOR;

        eval($source);
    }
}
