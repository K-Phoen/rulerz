<?php

namespace RulerZ\Executor\Polyfill;

use Hoa\Ruler\Model;

trait FilterBasedSatisfaction
{
    /**
     * {@inheritDoc}
     */
    abstract public function filter($target, Model $rule, array $parameters = []);

    /**
     * {@inheritDoc}
     */
    public function satisfies($target, Model $rule, array $parameters = [])
    {
        return count($this->filter($target, $rule, $parameters)) !== 0;
    }
}
