<?php

namespace spec\RulerZ\Target\DoctrineDBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use PhpSpec\ObjectBehavior;

use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Context;
use RulerZ\Model\Executor;
use RulerZ\Parser\Parser;

/**
 * TODO: refactor. It currently tests both the DoctrineDBAL and GenericSQLisitor classes.
 */
class DoctrineDBALSpec extends ObjectBehavior
{
    function it_supports_satisfies_mode_with_dbal_query_builders(QueryBuilder $builder)
    {
        $this->supports($builder, CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_supports_filter_mode_with_dbal_query_builders(QueryBuilder $builder)
    {
        $this->supports($builder, CompilationTarget::MODE_FILTER)->shouldReturn(true);
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
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->shouldHaveType('RulerZ\Model\Executor');

        $executorModel->getTraits()->shouldHaveCount(2);
        $executorModel->getCompiledRule()->shouldReturn('"1 = 1"');
    }

    function it_supports_parameters()
    {
        $rule = 'points > :nb_points and group IN [:admin_group, :super_admin_group]';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('"(points > :nb_points AND group IN (:admin_group, :super_admin_group))"');
    }

    function it_supports_custom_operators()
    {
        $rule = 'points > 30 and always_true()';

        $this->defineOperator('always_true', function() {
            throw new \LogicException('should not be called');
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('"(points > 30 AND ".call_user_func($operators["always_true"]).")"');
    }

    function it_supports_custom_inline_operators()
    {
        $rule = 'points > 30 and always_true()';

        $this->defineInlineOperator('always_true', function() {
            return '1 = 1';
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('"(points > 30 AND 1 = 1)"');
    }

    function it_implicitly_converts_unknown_operators()
    {
        $rule = 'points > 30 and always_true()';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), new Context());
        $executorModel->getCompiledRule()->shouldReturn('"(points > 30 AND always_true())"');
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
        return (new Parser())->parse($rule);
    }
}
