<?php

namespace RulerZ\Executor\Polyfill;

trait ExtendableExecutor
{
    /**
     * @var array A list of additionnal operators.
     */
    private $operators = [];

    /**
     * {@inheritDoc}
     */
    public function registerOperators(array $operators)
    {
        $this->operators = array_merge($this->operators, $operators);
    }

    /**
     * Return the custom operators.
     */
    public function getOperators()
    {
        return $this->operators;
    }
}
