<?php

use Hoa\Ruler\Ruler;

use Exception\TargetUnsupportedException;
use Executor\Executor;

class RulerZ
{
    private $executors = [];

    /**
     * Constructor.
     *
     * @param array $executors A list of executors to register immediatly.
     */
    public function __construct(array $executors = [])
    {
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
     * @param mixed $target     The target to filter.
     * @param Model $rule       The rule to apply.
     * @param array $parameters The parameters used in the rule.
     *
     * @return mixed The filtered target.
     */
    public function filter($target, $rule, array $parameters = array())
    {
        $executor = $this->findExecutor($target);
        $ast = $this->parse($rule);

        return $executor->filter($target, $ast, $parameters);
    }

    /**
     * Finds an executor supporting the given target.
     *
     * @param mixed $target The target to filter.
     *
     * @throws TargetUnsupportedException
     *
     * @return Executor
     */
    private function findExecutor($target)
    {
        foreach ($this->executors as $executor) {
            if ($executor->supports($target)) {
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
    private function parse($rule)
    {
        return Ruler::interprete($rule);
    }
}
