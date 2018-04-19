<?php

namespace RulerZ\Target\DoctrineORM;

use Doctrine\ORM\QueryBuilder;

use RulerZ\Compiler\Context;
use RulerZ\Executor\DoctrineORM\FilterTrait;
use RulerZ\Executor\Polyfill\FilterBasedSatisfaction;
use RulerZ\Target\AbstractSqlTarget;

class DoctrineORM extends AbstractSqlTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, string $mode): bool
    {
        return $target instanceof QueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createCompilationContext($target): Context
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
    public function getRuleIdentifierHint(string $rule, Context $context): string
    {
        $aliases = implode('', $context['root_aliases']);
        $entities = implode('', $context['root_entities']);

        return $aliases.$entities;
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
            FilterTrait::class,
            FilterBasedSatisfaction::class,
        ];
    }
}
