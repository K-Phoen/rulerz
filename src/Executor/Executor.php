<?php

namespace RulerZ\Executor;

use RulerZ\Context\ExecutionContext;

/**
 * An executor executes a rule against a target.
 */
interface Executor
{
    /**
     * Filters a target using the given rule and parameters.
     *
     * @param mixed            $target     The target.
     * @param array            $parameters The parameters used in the rule.
     * @param array<Xcallable> $operators  The available operators.
     * @param ExecutionContext $context    The execution context.
     *
     * @return \RulerZ\Result\FilterResult
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context);

    /**
     * Tells if a target satisfies the given rule and parameters.
     *
     * @param mixed            $target     The target.
     * @param array            $parameters The parameters used in the rule.
     * @param array<Xcallable> $operators  The available operators.
     * @param ExecutionContext $context    The execution context.
     *
     * @return boolean
     */
    public function satisfies($target, array $parameters, array $operators, ExecutionContext $context);
}
