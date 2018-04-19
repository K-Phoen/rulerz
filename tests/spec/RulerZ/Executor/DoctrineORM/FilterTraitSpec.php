<?php

declare(strict_types=1);

namespace spec\RulerZ\Executor\DoctrineORM;

use Doctrine\ORM\AbstractQuery as Query;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\DoctrineORMExecutorStub;
use spec\RulerZ\FilterResultMatcherTrait;

class FilterTraitSpec extends ObjectBehavior
{
    use FilterResultMatcherTrait;

    public function let()
    {
        $this->beAnInstanceOf(DoctrineORMExecutorStub::class);
    }

    public function it_can_apply_a_filter_on_a_target(QueryBuilder $target)
    {
        $dql = 'some_dql';

        DoctrineORMExecutorStub::$executeReturn = $dql;

        $target->setParameter('foo', 'bar')->shouldBeCalled();
        $target->andWhere($dql)->shouldBeCalled();

        $this->applyFilter($target, $parameters = ['foo' => 'bar'], $operators = [], new ExecutionContext())->shouldReturn($target);
    }

    public function it_can_apply_detected_joins(QueryBuilder $target)
    {
        $this->detectedJoins = [
            [
                'root' => 'root_alias',
                'column' => 'join_column',
                'as' => 'join_alias',
            ],
        ];
        $dql = 'some_dql';

        DoctrineORMExecutorStub::$executeReturn = $dql;

        $target->leftJoin('root_alias.join_column', 'join_alias')->shouldBeCalled();
        $target->setParameter('foo', 'bar')->shouldBeCalled();
        $target->andWhere($dql)->shouldBeCalled();

        $this->applyFilter($target, $parameters = ['foo' => 'bar'], $operators = [], new ExecutionContext())->shouldReturn($target);
    }

    public function it_call_findWhere_on_the_target(QueryBuilder $target, Query $query)
    {
        $this->detectedJoins = [
            [
                'root' => 'root_alias',
                'column' => 'join_column',
                'as' => 'join_alias',
            ],
        ];
        $dql = 'some_dql';
        $results = ['result'];

        DoctrineORMExecutorStub::$executeReturn = $dql;

        $target->leftJoin('root_alias.join_column', 'join_alias')->shouldBeCalled();
        $target->setParameter('foo', 'bar')->shouldBeCalled();
        $target->andWhere($dql)->shouldBeCalled();
        $target->getQuery()->willReturn($query);
        $query->getResult()->willReturn($results);

        $this->filter($target, $parameters = ['foo' => 'bar'], $operators = [], new ExecutionContext())->shouldReturnResults($results);
    }
}
