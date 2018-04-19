<?php

namespace spec\RulerZ\Target;

use RulerZ\Compiler\Context;
use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Model\Executor;
use RulerZ\Model\Rule;

trait ElasticsearchVisitorExamples
{
    abstract protected function parseRule(string $rule): Rule;

    public function it_can_returns_an_executor_model()
    {
        $rule = '1 = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->shouldHaveType(Executor::class);

        $executorModel->getTraits()->shouldHaveCount(2);
    }

    public function it_can_compile_a_simple_rule()
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

    public function it_handles_nested_accesses()
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

    public function it_throws_an_exception_when_calling_an_unknown_operator()
    {
        $this
            ->shouldThrow(OperatorNotFoundException::class)
            ->duringCompile($this->parseRule('operator_that_does_not_exist() = 42'), new Context());
    }
}
