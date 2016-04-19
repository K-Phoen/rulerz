<?php

namespace spec\RulerZ\Target\Native;

use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Context;
use RulerZ\Model\Executor;
use spec\RulerZ\Target\BaseTargetBehavior;

/**
 * TODO: refactor. It currently tests both the Native and NativeVisitor classes.
 */
class NativeSpec extends BaseTargetBehavior
{
    function it_supports_satisfies_mode()
    {
        $this->supports([], CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_supports_filtering_arrays()
    {
        $this->supports([], CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }

    function it_supports_satisfaction_tests_for_arrays()
    {
        $this->supports([], CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_can_returns_an_executor_model()
    {
        $rule = '1 = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->shouldHaveType('RulerZ\Model\Executor');

        $executorModel->getTraits()->shouldHaveCount(2);
        $executorModel->getCompiledRule()->shouldReturn('1 == 1');
    }

    function it_can_compile_a_simple_rule()
    {
        $rule = 'score = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('$target["score"] == 1');
    }

    function it_handles_nested_accesses()
    {
        $rule = 'stats.user.score >= 42';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('$target["stats"]["user"]["score"] >= 42');
    }

    function it_handles_custom_operators()
    {
        $rule = 'points >= 42 and always_true()';

        $this->defineOperator('always_true', function() {
            throw new \LogicException('should never be called');
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('($target["points"] >= 42 && call_user_func($operators["always_true"]))');
    }

    function it_handles_custom_operators_with_parameters()
    {
        $rule = 'points >= 42 and always_true(42)';

        $this->defineOperator('always_true', function() {
            throw new \LogicException('should never be called');
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('($target["points"] >= 42 && call_user_func($operators["always_true"], 42))');
    }

    function it_throws_an_exception_when_calling_an_unknown_operator()
    {
        $this
            ->shouldThrow('RulerZ\Exception\OperatorNotFoundException')
            ->duringCompile($this->parseRule('operator_that_does_not_exist() = 42'), new Context());
    }

    function it_handles_custom_inline_operators()
    {
        $rule = 'points >= 42 and always_true(42)';

        $this->defineInlineOperator('always_true', function($value) {
            return 'inline_always_true(' . $value . ')';
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('($target["points"] >= 42 && inline_always_true(42))');
    }

    public function unsupportedTypes()
    {
        return [
            'string',
            42,
            new \stdClass,
        ];
    }

    public function getMatchers()
    {
        return [
            'contain' => function ($subject, $text) {
                return strpos($subject, $text) !== false;
            },
        ];
    }
}
