<?php

namespace RulerZ\Target\DoctrineORM;

use Doctrine\ORM\QueryBuilder;

use RulerZ\Compiler\Context;
use RulerZ\Target\AbstractSqlTarget;

class DoctrineORM extends AbstractSqlTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createCompilationContext($target)
    {
        /* @var \Doctrine\ORM\QueryBuilder $target */

        return new Context([
            'root_aliases' => $target->getRootAliases(),
            'root_entities' => $target->getRootEntities(),
            'em' => $target->getEntityManager(),
            'joins' => $target->getDQLPart('join'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createVisitor(Context $context)
    {
        return new DoctrineORMVisitor($context, $this->getOperators(), $this->allowStarOperator);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\DoctrineORM\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }
}
