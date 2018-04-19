<?php

declare(strict_types=1);

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

    public function mergeWith(self $other): self
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
     */
    public function hasOperator(string $operator): bool
    {
        return isset($this->operators[$operator]);
    }

    /**
     * Define operators.
     *
     * @param array<callable> $operators A list of operators to add, each one being a collable.
     */
    public function defineOperators(array $operators): void
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
    public function defineOperator(string $operator, callable $transformer): void
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
     */
    protected function getOperator(string $operator): callable
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
    public function getOperators(): array
    {
        return $this->operators;
    }

    /**
     * Gets an inline-able operator.
     *
     * @param string $operator The operator's name.
     *
     * @throws OperatorNotFoundException
     */
    public function getInlineOperator(string $operator): callable
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
     */
    public function hasInlineOperator(string $operator): bool
    {
        return isset($this->inlineOperators[$operator]);
    }

    /**
     * Add inline operators.
     *
     * @param array<callable> $operators A list of inline operators to add.
     */
    public function defineInlineOperators(array $operators): void
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
    public function defineInlineOperator(string $operator, callable $transformer)
    {
        unset($this->operators[$operator]);
        $this->inlineOperators[$operator] = $transformer;
    }

    /**
     * Get the inline operators list.
     *
     * @return array<callable>
     */
    public function getInlineOperators(): array
    {
        return $this->inlineOperators;
    }
}
