<?php

namespace RulerZ\Target\Operators;

use RulerZ\Exception\OperatorNotFoundException;

class Definitions
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

    public function __construct(array $operators = [], array $inlineOperators = [])
    {
        $this->defineOperators($operators);
        $this->defineInlineOperators($inlineOperators);
    }

    public function mergeWith(self $other)
    {
        return new static(
            array_merge($this->operators, $other->operators),
            array_merge($this->inlineOperators, $other->inlineOperators)
        );
    }

    /**
     * Tells if an operator exists.
     *
     * @param string $operator The operator's name.
     *
     * @return bool
     */
    public function hasOperator($operator)
    {
        return isset($this->operators[$operator]);
    }

    /**
     * Define operators.
     *
     * @param array<callable> $operators A list of operators to add, each one being a collable.
     */
    public function defineOperators(array $operators)
    {
        foreach ($operators as $name => $callable) {
            $this->defineOperator($name, $callable);
        }
    }

    /**
     * Define an operator.
     *
     * @param string   $operator    The operator's name.
     * @param callable $transformer Callable.
     */
    public function defineOperator($operator, callable $transformer)
    {
        unset($this->inlineOperators[$operator]);
        $this->operators[$operator] = $transformer;
    }

    /**
     * Get an operator.
     *
     * @param string $operator The operator's name.
     *
     * @throws OperatorNotFoundException
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
     * @param string $operator The operator's name.
     *
     * @throws OperatorNotFoundException
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
     * @param string $operator The operator's name.
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
    public function defineInlineOperators(array $operators)
    {
        foreach ($operators as $name => $callable) {
            $this->defineInlineOperator($name, $callable);
        }
    }

    /**
     * Set an inline-able operator.
     *
     * @param string   $operator    The operator's name.
     * @param callable $transformer Callable.
     */
    public function defineInlineOperator($operator, callable $transformer)
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
