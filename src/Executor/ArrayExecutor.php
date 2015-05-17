<?php

namespace RulerZ\Executor;

use Hoa\Ruler\Model;
use Hoa\Ruler\Exception\visitor as visitorException;

use RulerZ\Context\ArrayContext;
use RulerZ\Context\ExecutionContext;
use RulerZ\Context\ObjectContext;
use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Visitor\ArrayVisitor;

/**
 * Execute a rule on an array.
 */
class ArrayExecutor implements ExtendableExecutor
{
    /**
     * @var ArrayVisitor
     */
    private $visitor;

    /**
     * Constructs the Array executor.
     *
     * @param array $operators A list of custom operators to register.
     */
    public function __construct(array $operators = [])
    {
        $this->visitor = new ArrayVisitor();

        $this->registerOperators($operators);
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, Model $rule, array $parameters, ExecutionContext $context)
    {
        return array_filter($target, function ($row) use ($rule, $parameters) {
            return $this->filterItem($row, $rule, $parameters);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function satisfies($target, Model $rule, array $parameters, ExecutionContext $context)
    {
        return $this->filterItem($target, $rule, $parameters) === true;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        if ($mode === self::MODE_FILTER) {
            return is_array($target) || $target instanceof \Traversable;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function registerOperators(array $operators)
    {
        foreach ($operators as $name => $callable) {
            $this->visitor->setOperator($name, $callable);
        }
    }

    /**
     * Test if an item matches the given rule.
     *
     * @param mixed $row        The row to test.
     * @param Model $rule       The rule to apply.
     * @param array $parameters The parameters used in the rule.
     *
     * @return boolean
     */
    private function filterItem($row, Model $rule, array $parameters)
    {
        $this->visitor->setContext($this->createContext($row));
        $this->visitor->setParameters($parameters);

        try {
            return $this->visitor->visit($rule);
        } catch (visitorException $e) {
            if (strpos($e->getMessage(), 'Operator') !== false) {
                throw new OperatorNotFoundException($e->getArguments()[0], $e->getMessage());
            }

            throw $e;
        }
    }

    /**
     * Create a context to be used by the visitor.
     *
     * @param mixed $row The row to test.
     *
     * @return \Hoa\Ruler\Context
     */
    private function createContext($row)
    {
        return is_array($row) ? new ArrayContext($row) : new ObjectContext($row);
    }
}
