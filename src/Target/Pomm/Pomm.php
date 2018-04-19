<?php

declare(strict_types=1);

namespace RulerZ\Target\Pomm;

use PommProject\ModelManager\Model\Model as PommModel;

use RulerZ\Compiler\Context;
use RulerZ\Executor\Polyfill\FilterBasedSatisfaction;
use RulerZ\Executor\Pomm\FilterTrait;
use RulerZ\Target\AbstractSqlTarget;
use RulerZ\Target\Operators\Definitions;

class Pomm extends AbstractSqlTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, string $mode): bool
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
            FilterTrait::class,
            FilterBasedSatisfaction::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators(): Definitions
    {
        return PommOperators::create(parent::getOperators());
    }
}
