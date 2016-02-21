<?php

namespace RulerZ\Compiler\Target\Sql;

use Doctrine\ORM\QueryBuilder;

use RulerZ\Compiler\Context;
use RulerZ\Compiler\Visitor\Sql\DoctrineQueryBuilderVisitor;

class DoctrineQueryBuilder extends AbstractSqlTarget
{
    /**
     * @inheritDoc
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder;
    }

    /**
     * @inheritDoc
     */
    public function createCompilationContext($target)
    {
        /** @var \Doctrine\ORM\QueryBuilder $target */

        return new Context([
            'root_aliases' => $target->getRootAliases(),
            'root_entities' => $target->getRootEntities(),
            'em' => $target->getEntityManager(),
            'joins' => $target->getDQLPart('join'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function createVisitor(Context $context)
    {
        return new DoctrineQueryBuilderVisitor($context, $this->getOperators(), $this->getInlineOperators(), $this->allowStarOperator);
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\DoctrineQueryBuilder\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }
}
