<?php

namespace spec\RulerZ\Target\DoctrineORM;

use Doctrine\ORM\QueryBuilder;

use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Context;
use RulerZ\Model\Executor;
use spec\RulerZ\Target\BaseTargetBehavior;

class DoctrineORMSpec extends BaseTargetBehavior
{
    function it_supports_satisfies_mode(QueryBuilder $qb)
    {
        $this->supports($qb, CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_can_filter_query_builders(QueryBuilder $qb)
    {
        $this->supports($qb, CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }

    function it_can_returns_an_executor_model()
    {
        $rule = '1 = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->shouldHaveType('RulerZ\Model\Executor');

        $executorModel->getTraits()->shouldHaveCount(3);
        $executorModel->getCompiledRule()->shouldReturn('"1 = 1"');
    }

    function it_prefixes_column_accesses_with_an_alias_placeholder()
    {
        $rule         = 'points >= 1';
        $expectedRule = '"%s.points >= 1"';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn($expectedRule);
    }

    function it_implicitly_converts_unknown_operators()
    {
        $rule        = 'points >= 42 and always_true(42)';
        $expectedDql = '"(%s.points >= 42 AND always_true(42))"';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn($expectedDql);
    }

    function it_supports_custom_inline_operators()
    {
        $rule        = 'points >= 42 and always_true(42)';
        $expectedDql = '"(%s.points >= 42 AND inline_always_true(42))"';

        $this->defineInlineOperator('always_true', function($value) {
            return 'inline_always_true(' . $value . ')';
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn($expectedDql);
    }
}