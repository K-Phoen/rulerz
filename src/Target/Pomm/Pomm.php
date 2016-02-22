<?php

namespace RulerZ\Target\Pomm;

use PommProject\ModelManager\Model\Model as PommModel;

use RulerZ\Compiler\Context;
use RulerZ\Target\AbstractSqlTarget;

class Pomm extends AbstractSqlTarget
{
    /**
     * @inheritDoc
     */
    public function supports($target, $mode)
    {
        // we make the assumption that pomm models use at least the
        // \PommProject\ModelManager\Model\ModelTrait\ReadQueries trait
        return $target instanceof PommModel;
    }

    /**
     * @inheritDoc
     */
    protected function createVisitor(Context $context)
    {
        return new PommVisitor($context, $this->getOperators(), $this->allowStarOperator);
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Pomm\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getOperators()
    {
        return PommOperators::create(parent::getOperators());
    }
}
