<?php

namespace spec\RulerZ\Executor;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery as Query;
use Doctrine\ORM\Query\Expr\Join;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Executor\Executor;

class DoctrineQueryBuilderExecutorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Executor\DoctrineQueryBuilderExecutor');
    }

    function it_supports_satisfies_mode(QueryBuilder $qb)
    {
        $this->supports($qb, Executor::MODE_SATISFIES)->shouldReturn(true);
    }

    function it_can_filter_query_builders(QueryBuilder $qb)
    {
        $this->supports($qb, Executor::MODE_FILTER)->shouldReturn(true);
    }

    function it_can_not_filter_other_types()
    {
        foreach ($this->unsupportedTypes() as $type) {
            $this->supports($type, Executor::MODE_FILTER)->shouldReturn(false);
        }
    }

    function it_can_filter_a_query_builder_with_a_rule(QueryBuilder $qb, Query $query, ExecutionContext $context)
    {
        $qb->getQuery()->willReturn($query);
        $qb->getRootAliases()->willReturn(['u']);
        $qb->getDQLPart('join')->willReturn([]);
        $query->getResult()->willReturn('result');

        $qb->andWhere('u.points > 30')->shouldBeCalled();

        $this->filter($qb, $this->getSimpleRule(), [], $context)->shouldReturn('result');
    }

    function it_supports_custom_operators(QueryBuilder $qb, Query $query, ExecutionContext $context)
    {
        $this->registerOperators([
            'always_true' => function() {
                return '1 = 1';
            }
        ]);

        $qb->getQuery()->willReturn($query);
        $qb->getRootAliases()->willReturn(['u']);
        $qb->getDQLPart('join')->willReturn([]);
        $query->getResult()->willReturn('result');

        $qb->andWhere('(u.points > 30 AND 1 = 1)')->shouldBeCalled();

        $this->filter($qb, $this->getCustomOperatorRule(), [], $context)->shouldReturn('result');
    }

    function it_implicitly_converts_unknown_operators(QueryBuilder $qb, Query $query, ExecutionContext $context)
    {
        $qb->getQuery()->willReturn($query);
        $qb->getRootAliases()->willReturn(['u']);
        $qb->getDQLPart('join')->willReturn([]);
        $query->getResult()->willReturn('result');

        $qb->andWhere('(u.points > 30 AND always_true())')->shouldBeCalled();

        $this->filter($qb, $this->getCustomOperatorRule(), [], $context)->shouldReturn('result');
    }

    function it_uses_joined_tables(QueryBuilder $qb, Query $query, Join $join, ExecutionContext $context)
    {
        $join->getJoin()->willReturn('u.group');
        $join->getAlias()->willReturn('g');
        $qb->getQuery()->willReturn($query);
        $qb->getRootAliases()->willReturn(['u']);
        $qb->getDQLPart('join')->willReturn([
            'u' => [$join]
        ]);
        $query->getResult()->willReturn('result');

        $qb->join('u.group', 'j_group')->shouldNotBeCalled();
        $qb->andWhere("(u.points > 30 AND g.name = 'admin')")->shouldBeCalled();

        $this->filter($qb, $this->getJoinRule(), [], $context)->shouldReturn('result');
    }

    function it_automatically_join_tables(QueryBuilder $qb, Query $query, ExecutionContext $context)
    {
        $qb->getQuery()->willReturn($query);
        $qb->getRootAliases()->willReturn(['u']);
        $qb->getDQLPart('join')->willReturn([]);
        $query->getResult()->willReturn('result');

        $qb->join('u.group', 'j_group')->shouldBeCalled();
        $qb->andWhere("(u.points > 30 AND j_group.name = 'admin')")->shouldBeCalled();

        $this->filter($qb, $this->getJoinRule(), [], $context)->shouldReturn('result');
    }

    function it_uses_selected_tables(QueryBuilder $qb, Query $query, ExecutionContext $context)
    {
        $qb->getQuery()->willReturn($query);
        $qb->getRootAliases()->willReturn(['u', 'other_table']);
        $qb->getDQLPart('join')->willReturn([]);
        $query->getResult()->willReturn('result');

        $qb->andWhere("(u.points > 30 AND other_table.foo = 'bar')")->shouldBeCalled();

        $this->filter($qb, $this->getSelectedTableRule(), [], $context)->shouldReturn('result');
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

    private function getSimpleRule()
    {
        // serialized rule for "points > 30"
        $rule = 'O:21:"Hoa\\Ruler\\Model\\Model":1:{s:8:"' . "\0" . '*' . "\0" . '_root";O:24:"Hoa\\Ruler\\Model\\Operator":3:{s:8:"' . "\0" . '*' . "\0" . '_name";s:1:">";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:27:"Hoa\\Ruler\\Model\\Bag\\Context":2:{s:6:"' . "\0" . '*' . "\0" . '_id";s:6:"points";s:14:"' . "\0" . '*' . "\0" . '_dimensions";a:0:{}}i:1;O:26:"Hoa\\Ruler\\Model\\Bag\\Scalar":1:{s:9:"' . "\0" . '*' . "\0" . '_value";i:30;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;}}';

        return unserialize($rule);
    }

    private function getCustomOperatorRule()
    {
        // serialized rule for "points > 30 and always_true()"
        $rule = 'O:21:"Hoa\\Ruler\\Model\\Model":1:{s:8:"' . "\0" . '*' . "\0" . '_root";O:24:"Hoa\\Ruler\\Model\\Operator":3:{s:8:"' . "\0" . '*' . "\0" . '_name";s:3:"and";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:24:"Hoa\\Ruler\\Model\\Operator":3:{s:8:"' . "\0" . '*' . "\0" . '_name";s:1:">";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:27:"Hoa\\Ruler\\Model\\Bag\\Context":2:{s:6:"' . "\0" . '*' . "\0" . '_id";s:6:"points";s:14:"' . "\0" . '*' . "\0" . '_dimensions";a:0:{}}i:1;O:26:"Hoa\\Ruler\\Model\\Bag\\Scalar":1:{s:9:"' . "\0" . '*' . "\0" . '_value";i:30;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;}i:1;O:24:"Hoa\\Ruler\\Model\\Operator":3:{s:8:"' . "\0" . '*' . "\0" . '_name";s:11:"always_true";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:0:{}s:12:"' . "\0" . '*' . "\0" . '_function";b:1;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;}}';

        return unserialize($rule);
    }

    private function getJoinRule()
    {
        // serialized rule for "points > 30 and group.name = 'admin'"
        $rule = 'O:21:"Hoa\\Ruler\\Model\\Model":1:{s:8:"' . "\0" . '*' . "\0" . '_root";O:24:"Hoa\\Ruler\\Model\\Operator":4:{s:8:"' . "\0" . '*' . "\0" . '_name";s:3:"and";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:24:"Hoa\\Ruler\\Model\\Operator":4:{s:8:"' . "\0" . '*' . "\0" . '_name";s:1:">";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:27:"Hoa\\Ruler\\Model\\Bag\\Context":2:{s:6:"' . "\0" . '*' . "\0" . '_id";s:6:"points";s:14:"' . "\0" . '*' . "\0" . '_dimensions";a:0:{}}i:1;O:26:"Hoa\\Ruler\\Model\\Bag\\Scalar":1:{s:9:"' . "\0" . '*' . "\0" . '_value";i:30;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;s:12:"' . "\0" . '*' . "\0" . '_laziness";b:0;}i:1;O:24:"Hoa\\Ruler\\Model\\Operator":4:{s:8:"' . "\0" . '*' . "\0" . '_name";s:1:"=";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:27:"Hoa\\Ruler\\Model\\Bag\\Context":2:{s:6:"' . "\0" . '*' . "\0" . '_id";s:5:"group";s:14:"' . "\0" . '*' . "\0" . '_dimensions";a:1:{i:0;a:2:{i:0;i:1;i:1;s:4:"name";}}}i:1;O:26:"Hoa\\Ruler\\Model\\Bag\\Scalar":1:{s:9:"' . "\0" . '*' . "\0" . '_value";s:5:"admin";}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;s:12:"' . "\0" . '*' . "\0" . '_laziness";b:0;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;s:12:"' . "\0" . '*' . "\0" . '_laziness";b:1;}}';

        return unserialize($rule);
    }

    private function getSelectedTableRule()
    {
        // serialized rule for "points > 30 and other_table.foo = 'bar'"
        $rule = 'O:21:"Hoa\\Ruler\\Model\\Model":1:{s:8:"' . "\0" . '*' . "\0" . '_root";O:24:"Hoa\\Ruler\\Model\\Operator":4:{s:8:"' . "\0" . '*' . "\0" . '_name";s:3:"and";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:24:"Hoa\\Ruler\\Model\\Operator":4:{s:8:"' . "\0" . '*' . "\0" . '_name";s:1:">";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:27:"Hoa\\Ruler\\Model\\Bag\\Context":2:{s:6:"' . "\0" . '*' . "\0" . '_id";s:6:"points";s:14:"' . "\0" . '*' . "\0" . '_dimensions";a:0:{}}i:1;O:26:"Hoa\\Ruler\\Model\\Bag\\Scalar":1:{s:9:"' . "\0" . '*' . "\0" . '_value";i:30;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;s:12:"' . "\0" . '*' . "\0" . '_laziness";b:0;}i:1;O:24:"Hoa\\Ruler\\Model\\Operator":4:{s:8:"' . "\0" . '*' . "\0" . '_name";s:1:"=";s:13:"' . "\0" . '*' . "\0" . '_arguments";a:2:{i:0;O:27:"Hoa\\Ruler\\Model\\Bag\\Context":2:{s:6:"' . "\0" . '*' . "\0" . '_id";s:11:"other_table";s:14:"' . "\0" . '*' . "\0" . '_dimensions";a:1:{i:0;a:2:{i:0;i:1;i:1;s:3:"foo";}}}i:1;O:26:"Hoa\\Ruler\\Model\\Bag\\Scalar":1:{s:9:"' . "\0" . '*' . "\0" . '_value";s:3:"bar";}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;s:12:"' . "\0" . '*' . "\0" . '_laziness";b:0;}}s:12:"' . "\0" . '*' . "\0" . '_function";b:0;s:12:"' . "\0" . '*' . "\0" . '_laziness";b:1;}}';

        return unserialize($rule);
    }
}
