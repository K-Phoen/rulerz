<?php

namespace RulerZ\Target\DoctrineDBAL;

use Doctrine\DBAL\Query\QueryBuilder;

use RulerZ\Compiler\Context;
use RulerZ\Target\AbstractSqlTarget;
use RulerZ\Target\GenericSqlVisitor;

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
