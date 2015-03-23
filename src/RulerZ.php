<?php

namespace RulerZ;

use Hoa\Ruler\Ruler;

use RulerZ\Context\ExecutionContext;
use RulerZ\Exception\TargetUnsupportedException;
use RulerZ\Executor\Executor;
use RulerZ\Interpreter\Interpreter;
use RulerZ\Spec\Specification;

class RulerZ
{
    /**
     * @var Interpreter $interpreter
     */
    private $interpreter;

    /**
     * @var array $executors
     */
    private $executors = [];

    /**
     * Constructor.
     *
     * @param array $executors A list of executors to register immediatly.
     */
    public function __construct(Interpreter $interpreter, array $executors = [])
    {
        $this->interpreter = $interpreter;

        foreach ($executors as $executor) {
            $this->registerExecutor($executor);
        }
    }

    /**
     * Registers a new executor.
     *
     * @param Executor $executor The executor to register.
     */
    public function registerExecutor(Executor $executor)
    {
        $this->executors[] = $executor;
    }

    /**
     * Filters a target using the given rule and parameters.
     * The executor to use is determined at runtime using the registered ones.
     *
     * @param mixed  $target           The target to filter.
     * @param string $rule             The rule to apply.
     * @param array  $parameters       The parameters used in the rule.
     * @param array  $executionContext The execution context.
     *
     * @return mixed The filtered target.
     */
    public function filter($target, $rule, array $parameters = [], array $executionContext = [])
    {
        $executor = $this->findExecutor($target, Executor::MODE_FILTER);
        $ast      = $this->interpret($rule);

        return $executor->filter($target, $ast, $parameters, new ExecutionContext($executionContext));
    }

    /**
     * Filters a target using the given specification.
     * The executor to use is determined at runtime using the registered ones.
     *
     * @param mixed         $target           The target to filter.
     * @param Specification $spec             The specification to apply.
     * @param array         $executionContext The execution context.
     *
     * @return mixed The filtered target.
     */
    public function filterSpec($target, Specification $spec, array $executionContext = [])
    {
        return $this->filter($target, $spec->getRule(), $spec->getParameters(), $executionContext);
    }

    /**
     * Tells if a target satisfies the given rule and parameters.
     * The executor to use is determined at runtime using the registered ones.
     *
     * @param mixed  $target           The target.
     * @param string $rule             The rule to test.
     * @param array  $parameters       The parameters used in the rule.
     * @param array  $executionContext The execution context.
     *
     * @return boolean
     */
    public function satisfies($target, $rule, array $parameters = [], array $executionContext = [])
    {
        $executor = $this->findExecutor($target, Executor::MODE_SATISFIES);
        $ast      = $this->interpret($rule);

        return $executor->satisfies($target, $ast, $parameters, new ExecutionContext($executionContext));
    }

    /**
     * Tells if a target satisfies the given specification.
     * The executor to use is determined at runtime using the registered ones.
     *
     * @param mixed         $target The target.
     * @param Specification $spec   The specification to use.
     *
     * @return boolean
     */
    public function satisfiesSpec($target, Specification $spec, array $executionContext = [])
    {
        return $this->satisfies($target, $spec->getRule(), $spec->getParameters(), $executionContext);
    }

    /**
     * Finds an executor supporting the given target.
     *
     * @param mixed  $target The target to filter.
     * @param string $mode   The execution mode (MODE_FILTER or MODE_SATISFIES).
     *
     * @throws TargetUnsupportedException
     *
     * @return Executor
     */
    private function findExecutor($target, $mode)
    {
        foreach ($this->executors as $executor) {
            if ($executor->supports($target, $mode)) {
                return $executor;
            }
        }

        throw new TargetUnsupportedException('The given target is not supported.');
    }

    /**
     * Parses the rule into an equivalent AST.
     *
     * @param string $rule The rule represented as a string.
     *
     * @return \Hoa\Ruler\Model
     */
    private function interpret($rule)
    {
        return $this->interpreter->interpret($rule);
    }
}
