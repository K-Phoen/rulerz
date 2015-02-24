<?php

namespace RulerZ\Visitor\Polyfill;

use Hoa\Core\Consistency\Xcallable;

use RulerZ\Exception\OperatorNotFoundException;

trait CustomOperators
{
    /**
     * List of operators.
     *
     * @var array
     */
    private $operators = [];

    /**
     * Set an operator.
     *
     * @param string   $operator    Operator.
     * @param callable $transformer Callable.
     *
     * @return DoctrineQueryBuilderVisitor
     */
    public function setOperator($operator, callable $transformer)
    {
        $this->operators[$operator] = $transformer;

        return $this;
    }

    /**
     * Check if an operator exists.
     *
     * @param string $operator Operator.
     *
     * @return bool
     */
    public function operatorExists($operator)
    {
        return true === array_key_exists($operator, $this->operators);
    }

    /**
     * Get an operator.
     *
     * @param string $operator Operator.
     *
     * @return Xcallable
     */
    private function getOperator($operator)
    {
        if (false === $this->operatorExists($operator)) {
            throw new OperatorNotFoundException($operator, 'Operator "%s" does not exist.');
        }

        $handle = &$this->operators[$operator];

        if (!$handle instanceof Xcallable) {
            $handle = xcallable($handle);
        }

        return $this->operators[$operator];
    }
}
