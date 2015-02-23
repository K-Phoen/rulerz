<?php

namespace RulerZ\Executor\Polyfill;

use Hoa\Ruler\Model;

trait FilterBasedSatisfaction
{
    /**
     * {@inheritDoc}
     */
    public function satisfies($target, Model $rule, array $parameters = [])
    {
        return count($this->filter($target, $rule, $parameters)) !== 0;
    }
}
