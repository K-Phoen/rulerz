<?php

namespace spec\RulerZ\Compiler\Target\Sql;

use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;

use RulerZ\Compiler\Target\CompilationTarget;
use RulerZ\Compiler\Target\Sql\DoctrineQueryBuilderVisitor;
use RulerZ\Model\Executor;
use RulerZ\Parser\HoaParser;

class DoctrineQueryBuilderVisitorSpec extends ObjectBehavior
{
    function it_supports_satisfies_mode(QueryBuilder $qb)
    {
        $this->supports($qb, CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_can_filter_query_builders(QueryBuilder $qb)
    {
        $this->supports($qb, CompilationTarget::MODE_FILTER)->shouldReturn(true);
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
        $executorModel->getCompiledRule()->shouldReturn('"1 = 1"');
    }

    function it_prefixes_column_accesses_with_an_alias_placeholder()
    {
        $rule         = 'points >= 1';
        $expectedRule = sprintf('"%s.points >= 1"', DoctrineQueryBuilderVisitor::ROOT_ALIAS_PLACEHOLDER);

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule));
        $executorModel->getCompiledRule()->shouldReturn($expectedRule);
    }

    function it_implicitly_converts_unknown_operators()
    {
        $rule        = 'points >= 42 and always_true(42)';
        $expectedDql = sprintf('"(%s.points >= 42 AND always_true(42))"', DoctrineQueryBuilderVisitor::ROOT_ALIAS_PLACEHOLDER);

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule));
        $executorModel->getCompiledRule()->shouldReturn($expectedDql);
    }

    function it_supports_custom_operators()
    {
        $rule        = 'points >= 42 and always_true(42)';
        $expectedDql = sprintf('"(%s.points >= 42 AND inline_always_true(42))"', DoctrineQueryBuilderVisitor::ROOT_ALIAS_PLACEHOLDER);

        $this->setInlineOperator('always_true', function($value) {
            return 'inline_always_true(' . $value . ')';
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule));
        $executorModel->getCompiledRule()->shouldReturn($expectedDql);
    }

    private function unsupportedTypes()
    {
        return [
            'string',
            42,
            new \stdClass,
        ];
    }

    private function parseRule($rule)
    {
        return (new HoaParser())->parse($rule);
    }
}
