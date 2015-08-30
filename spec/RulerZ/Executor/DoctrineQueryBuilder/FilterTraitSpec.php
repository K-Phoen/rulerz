<?php

namespace spec\RulerZ\Executor\DoctrineQueryBuilder;

use Doctrine\ORM\AbstractQuery as Query;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Stub\Executor\DoctrineExecutorStub;

class FilterTraitSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\DoctrineExecutorStub');
    }

    function it_call_findWhere_on_the_target(QueryBuilder $target, Query $query)
    {
        $dql         = 'dql query with root entity alias: @@_ROOT_ALIAS_@@';
        $modifiedDql = 'dql query with root entity alias: root_alias';
        $results     = ['result'];

        DoctrineExecutorStub::$executeReturn = $dql;

        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getQuery()->willReturn($query);
        $query->getResult()->willReturn($results);

        $target->setParameter('foo', 'bar')->shouldBeCalled();
        $target->andWhere($modifiedDql)->shouldBeCalled();

        $this->filter($target, $parameters = ['foo' => 'bar'], $operators = [], new ExecutionContext())->shouldReturn($results);
    }
}
