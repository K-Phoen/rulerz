<?php

declare(strict_types=1);

namespace spec\RulerZ\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Compiler;
use RulerZ\Compiler\Context as CompilationContext;
use RulerZ\Compiler\EvalEvaluator;
use RulerZ\Executor\Executor;

class CompilerSpec extends ObjectBehavior
{
    public function let(EvalEvaluator $evaluator)
    {
        $this->beConstructedWith($evaluator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Compiler::class);
    }

    public function it_delegates_code_evaluation_when_the_executor_is_not_loaded(EvalEvaluator $evaluator, CompilationTarget $target)
    {
        $rule = 'points > 42';
        $context = new CompilationContext();
        $self = $this;
        $expectedExecutorName = null;

        $target->getRuleIdentifierHint($rule, $context)->willReturn('some-identifier');

        $evaluator->evaluate(Argument::any(), Argument::any())->will(function ($args) use ($self, &$expectedExecutorName) {
            $expectedExecutorName = 'Executor_'.$args[0];

            $self->loadExecutor($expectedExecutorName);
        });

        // the compiler returns an instance of the compiled Executor
        $executor = $this->compile($rule, $target, $context);
        $executor->shouldHaveType('RulerZ\Compiled\Executor\\'.$expectedExecutorName);
        $executor->shouldHaveType(Executor::class);

        // and calling the compiler again does not fail
        $executor = $this->compile($rule, $target, $context);
        $executor->shouldHaveType('RulerZ\Compiled\Executor\\'.$expectedExecutorName);
        $executor->shouldHaveType(Executor::class);
    }

    private function loadExecutor($classnName)
    {
        $source = <<<EXECUTOR
namespace RulerZ\Compiled\Executor;

use RulerZ\Executor\Executor;
use RulerZ\Context\ExecutionContext;
        
class $classnName implements Executor
{
    public function applyFilter(\$target, array \$parameters, array \$operators, ExecutionContext \$context)
    {
    }

    public function filter(\$target, array \$parameters, array \$operators, ExecutionContext \$context)
    {
    }

    public function satisfies(\$target, array \$parameters, array \$operators, ExecutionContext \$context): bool
    {
        return false;
    }
}
EXECUTOR;

        eval($source);
    }
}
