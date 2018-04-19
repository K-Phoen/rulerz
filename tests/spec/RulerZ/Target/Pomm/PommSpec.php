<?php

namespace spec\RulerZ\Target\Pomm;

use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Context;
use RulerZ\Model\Executor;
use RulerZ\Stub\ModelStub;
use spec\RulerZ\Target\BaseTargetBehavior;

/**
 * TODO: refactor. It currently tests both the Pomm and PommVisitor classes.
 */
class PommSpec extends BaseTargetBehavior
{
    public function it_supports_satisfies_mode()
    {
        $this->supports(new ModelStub(), CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    public function it_can_filter_where_clauses()
    {
        $this->supports(new ModelStub(), CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }

    public function it_can_returns_an_executor_model()
    {
        $rule = '1 = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->shouldHaveType(Executor::class);

        $executorModel->getTraits()->shouldHaveCount(2);
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("1 = 1", []))');
    }

    public function it_supports_parameters()
    {
        $rule = 'points > :nb_points and group IN [:admin_group, :super_admin_group]';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("points > $*", [$parameters["nb_points"]]))->andWhere((new \PommProject\Foundation\Where("group IN ($*, $*)", [$parameters["admin_group"], $parameters["super_admin_group"]])))');
    }

    public function it_supports_custom_operators()
    {
        $rule = 'points > 30 and always_true()';

        $this->defineOperator('always_true', function () {
            throw new \LogicException('should not be called');
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("points > 30", []))->andWhere((new \PommProject\Foundation\Where(call_user_func($operators["always_true"]), [])))');
    }

    public function it_supports_custom_inline_operators()
    {
        $rule = 'points > 30 and always_true()';

        $this->defineInlineOperator('always_true', function () {
            return '1 = 1';
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("points > 30", []))->andWhere((new \PommProject\Foundation\Where("1 = 1", [])))');
    }

    public function it_implicitly_converts_unknown_operators()
    {
        $rule = 'points > 30 and always_true()';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('(new \PommProject\Foundation\Where("points > 30", []))->andWhere((new \PommProject\Foundation\Where("always_true()", [])))');
    }
}
