<?php

namespace spec\RulerZ\Executor\DoctrineDBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\DoctrineDBALExecutorStub;
use spec\RulerZ\FilterResultMatcherTrait;

class FilterTraitSpec extends ObjectBehavior
{
    use FilterResultMatcherTrait;

    function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\DoctrineDBALExecutorStub');
    }

    function it_can_apply_a_filter_on_a_target(QueryBuilder $target)
    {
        $dql = 'some_dql';

        DoctrineDBALExecutorStub::$executeReturn = $dql;

        $target->setParameter('foo', 'bar', $type = null)->shouldBeCalled();
        $target->andWhere($dql)->shouldBeCalled();

        $this->applyFilter($target, $parameters = ['foo' => 'bar'], $operators = [], new ExecutionContext())->shouldReturn($target);
    }

    function it_call_findWhere_on_the_target(QueryBuilder $target, Statement $statement)
    {
        $dql = 'some_dql';
        $results = ['result'];

        DoctrineDBALExecutorStub::$executeReturn = $dql;

        $target->setParameter('foo', 'bar', $type = null)->shouldBeCalled();
        $target->andWhere($dql)->shouldBeCalled();
        $target->execute()->willReturn($statement);
        $statement->fetchAll()->willReturn($results);

        $this->filter($target, $parameters = ['foo' => 'bar'], $operators = [], new ExecutionContext())->shouldReturnResults($results);
    }
}
