<?php

namespace spec\RulerZ\Compiler\Target\Sql;

use PhpSpec\ObjectBehavior;

use RulerZ\Compiler\Target\CompilationTarget;
use RulerZ\Model\Executor;
use RulerZ\Parser\HoaParser;
use RulerZ\Stub\ModelStub;

class PommVisitorSpec extends ObjectBehavior
{
    function it_supports_satisfies_mode()
    {
        $this->supports(new ModelStub(), CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_can_filter_where_clauses()
    {
        $this->supports(new ModelStub(), CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }

    function it_can_not_filter_other_types()
    {
        foreach ($this->unsupportedTypes() as $type) {
            $this->supports($type, CompilationTarget::MODE_FILTER)->shouldReturn(false);
        }
    }

    function it_can_returns_an_executor_model()
    {
        $rule = '1 = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule));
        $executorModel->shouldHaveType('RulerZ\Model\Executor');

        $executorModel->getTraits()->shouldHaveCount(2);
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("1 = 1", []))');
    }

    function it_supports_parameters()
    {
        $rule = 'points > :nb_points and group IN [:admin_group, :super_admin_group]';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule));
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("points > $*", [$parameters["nb_points"]]))->andWhere((new \PommProject\Foundation\Where("group IN ($*, $*)", [$parameters["admin_group"], $parameters["super_admin_group"]])))');
    }

    function it_supports_custom_operators()
    {
        $rule = 'points > 30 and always_true()';

        $this->setOperator('always_true', function() {
            throw new \LogicException('should not be called');
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule));
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("points > 30", []))->andWhere((new \PommProject\Foundation\Where(call_user_func($operators["always_true"]), [])))');
    }

    function it_supports_custom_inline_operators()
    {
        $rule = 'points > 30 and always_true()';

        $this->setInlineOperator('always_true', function() {
            return '1 = 1';
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule));
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("points > 30", []))->andWhere((new \PommProject\Foundation\Where("1 = 1", [])))');
    }

    function it_implicitly_converts_unknown_operators()
    {
        $rule = 'points > 30 and always_true()';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule));
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("points > 30", []))->andWhere((new \PommProject\Foundation\Where("always_true()", [])))');
    }

    private function unsupportedTypes()
    {
        return [
            'string',
            42,
            new \stdClass,
            [],
        ];
    }

    private function parseRule($rule)
    {
        return (new HoaParser())->parse($rule);
    }
}
