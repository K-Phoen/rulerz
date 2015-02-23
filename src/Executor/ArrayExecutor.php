<?php

namespace RulerZ\Executor;

use Hoa\Ruler\Context as ArrayContext;
use Hoa\Ruler\Model;
use Hoa\Ruler\Visitor\Asserter;

use RulerZ\Context\ObjectContext;

/**
 * Execute a rule on an array.
 */
class ArrayExecutor implements ExtendableExecutor
{
    /**
     * @var Asserter
     */
    private $asserter;

    /**
     * Constructs the Array executor.
     *
     * @param array $operators A list of custom operators to register.
     */
    public function __construct(array $operators = [])
    {
        $this->asserter = new Asserter();

        $this->registerOperators($operators);
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, Model $rule, array $parameters = [])
    {
        $newParameters = $this->prepareParameters($parameters);

        return array_filter($target, function ($row) use ($rule, $newParameters) {
            return $this->filterItem($row, $rule, $newParameters);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function satisfies($target, Model $rule, array $parameters = [])
    {
        $newParameters = $this->prepareParameters($parameters);

        return $this->filterItem($target, $rule, $newParameters) === true;
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
            $this->asserter->setOperator($name, $callable);
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
        $this->asserter->setContext($this->createContext($row, $parameters));

        return $this->asserter->visit($rule);
    }

    /**
     * Create a context to be used by the asserter.
     *
     * @param mixed $row        The row to test.
     * @param array $parameters The parameters used in the rule.
     *
     * @return \Hoa\Ruler\Context
     */
    private function createContext($row, array $parameters)
    {
        return is_array($row)
            ? new ArrayContext(array_merge($row, $parameters))
            : new ObjectContext($row, $parameters);
    }

    /**
     * Prepare the parameters so that they can be used in the asserter.
     *
     * @param array $parameters The parameters used in the rule.
     *
     * @return array
     */
    private function prepareParameters(array $parameters)
    {
        $newParameters = [];

        foreach ($parameters as $name => $value) {
            $newParameters[':'.$name] = $value;
        }

        return $newParameters;
    }
}
