<?php

namespace spec\RulerZ\Target;

use RulerZ\Compiler\Context;
use RulerZ\Model\Executor;

trait ElasticsearchVisitorExamples
{
    abstract protected function parseRule($rule);

    function it_can_returns_an_executor_model()
    {
        $rule = '1 = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->shouldHaveType('RulerZ\Model\Executor');

        $executorModel->getTraits()->shouldHaveCount(2);
    }

    function it_can_compile_a_simple_rule()
    {
        $rule = 'points > 30';
        $expectedQuery = <<<'QUERY'
[
    'bool' => ['must' => [
                'range' => [
                    'points' => ['gt' => 30],
                ]
            ]]
]
QUERY;

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn($expectedQuery);
    }

    function it_handles_nested_accesses()
    {
        $rule = 'user.stats.points > 30';
        $expectedQuery = <<<'QUERY'
[
    'bool' => ['must' => [
                'range' => [
                    'user.stats.points' => ['gt' => 30],
                ]
            ]]
]
QUERY;

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn($expectedQuery);
    }

    function it_throws_an_exception_when_calling_an_unknown_operator()
    {
        $this
            ->shouldThrow('RulerZ\Exception\OperatorNotFoundException')
            ->duringCompile($this->parseRule('operator_that_does_not_exist() = 42'), new Context());
    }
}
