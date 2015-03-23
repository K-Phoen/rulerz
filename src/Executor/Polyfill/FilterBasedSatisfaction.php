<?php

namespace RulerZ\Executor\Polyfill;

use Hoa\Ruler\Model;

use RulerZ\Context\ExecutionContext;

trait FilterBasedSatisfaction
{
    /**
     * {@inheritDoc}
     */
    abstract public function filter($target, Model $rule, array $parameters, ExecutionContext $context);

    /**
     * {@inheritDoc}
     */
    public function satisfies($target, Model $rule, array $parameters, ExecutionContext $context)
    {
        return count($this->filter($target, $rule, $parameters, $context)) !== 0;
    }
}
