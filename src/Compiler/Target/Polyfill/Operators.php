<?php

namespace RulerZ\Compiler\Target\Polyfill;

use RulerZ\Exception\OperatorNotFoundException;

trait Operators
{
    /**
     * List of operators.
     *
     * @var array
     */
    private $operators = [];

    /**
     * List of inline-able operators.
     *
     * @var array
     */
    private $inlineOperators = [];

    /**
     * Tells if an operator exists.
     *
     * @param string $operator Operator.
     *
     * @return bool
     */
    public function hasOperator($operator)
    {
        return isset($this->operators[$operator]);
    }

    /**
     * Add operators.
     *
     * @param array $operators A list of operators to add.
     *
     * @return self
     */
    public function setOperators(array $operators)
    {
        foreach ($operators as $name => $callable) {
            $this->setOperator($name, $callable);
        }
    }

    /**
     * Set an operator.
     *
     * @param string   $operator    Operator.
     * @param callable $transformer Callable.
     *
     * @return self
     */
    public function setOperator($operator, callable $transformer)
    {
        unset($this->inlineOperators[$operator]);
        $this->operators[$operator] = $transformer;
    }

    /**
     * Get an operator.
     *
     * @param string $operator Operator.
     *
     * @return callable
     */
    protected function getOperator($operator)
    {
        if (!$this->hasOperator($operator)) {
            throw new OperatorNotFoundException($operator, sprintf('Operator "%s" does not exist.', $operator));
        }

        return $this->operators[$operator];
    }

    /**
     * Get the operators list.
     *
     * @return array<callable>
     */
    public function getOperators()
    {
        return $this->operators;
    }

    /**
     * Gets an inline-able operator.
     *
     * @param string $operator Operator.
     *
     * @return callable
     */
    public function getInlineOperator($operator)
    {
        if (!$this->hasInlineOperator($operator)) {
            throw new OperatorNotFoundException($operator, sprintf('Inline operator "%s" does not exist.', $operator));
        }

        return $this->inlineOperators[$operator];
    }

    /**
     * Tells if an inline-able operator exists.
     *
     * @param string $operator Operator.
     *
     * @return bool
     */
    public function hasInlineOperator($operator)
    {
        return isset($this->inlineOperators[$operator]);
    }

    /**
     * Add inline operators.
     *
     * @param array<callable> $operators A list of inline operators to add.
     */
    public function setInlineOperators(array $operators)
    {
        foreach ($operators as $name => $callable) {
            $this->setInlineOperator($name, $callable);
        }
    }

    /**
     * Set an inline-able operator.
     *
     * @param string   $operator    Operator.
     * @param callable $transformer Callable.
     */
    public function setInlineOperator($operator, callable $transformer)
    {
        unset($this->operators[$operator]);
        $this->inlineOperators[$operator] = $transformer;
    }

    /**
     * Get the inline operators list.
     *
     * @return array<callable>
     */
    public function getInlineOperators()
    {
        return $this->inlineOperators;
    }
}
