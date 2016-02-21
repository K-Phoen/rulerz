<?php

namespace RulerZ\Compiler\Target\Sql;

use Doctrine\DBAL\Query\QueryBuilder;

use RulerZ\Compiler\Context;
use RulerZ\Compiler\Visitor\Sql\GenericSqlVisitor;

class DoctrineDBAL extends AbstractSqlTarget
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
    protected function createVisitor(Context $context)
    {
        return new GenericSqlVisitor($context, $this->getOperators(), $this->getInlineOperators(), $this->allowStarOperator);
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\DoctrineDBAL\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }
}
