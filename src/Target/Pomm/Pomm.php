<?php

namespace RulerZ\Target\Pomm;

use PommProject\ModelManager\Model\Model as PommModel;

use RulerZ\Compiler\Context;
use RulerZ\Target\AbstractSqlTarget;

class Pomm extends AbstractSqlTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, $mode)
    {
        // we make the assumption that pomm models use at least the
        // \PommProject\ModelManager\Model\ModelTrait\ReadQueries trait
        return $target instanceof PommModel;
    }

    /**
     * {@inheritdoc}
     */
    protected function createVisitor(Context $context)
    {
        return new PommVisitor($context, $this->getOperators(), $this->allowStarOperator);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Pomm\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return PommOperators::create(parent::getOperators());
    }
}
