<?php

namespace RulerZ\Executor;

use Hoa\Ruler\Model;

use RulerZ\Context\ExecutionContext;

/**
 * An executor executes a rule against a target.
 */
interface Executor
{
    const MODE_FILTER    = 'filter';
    const MODE_SATISFIES = 'satisfies';

    /**
     * Filters a target using the given rule and parameters.
     *
     * @param mixed $target     The target to filter.
     * @param Model $rule       The rule to apply.
     * @param array $parameters The parameters used in the rule.
     *
     * @return mixed The filtered target.
     */
    public function filter($target, Model $rule, array $parameters, ExecutionContext $context);

    /**
     * Tells if aa target satisfies the given rule and parameters.
     *
     * @param mixed $target     The target.
     * @param Model $rule       The rule to test.
     * @param array $parameters The parameters used in the rule.
     *
     * @return boolean
     */
    public function satisfies($target, Model $rule, array $parameters, ExecutionContext $context);

    /**
     * Indicates whether the given object can be filtered by the executor.
     *
     * @param mixed  $target The target to test.
     * @param string $mode   The execution mode (MODE_FILTER or MODE_SATISFIES).
     *
     * @return boolean
     */
    public function supports($target, $mode);
}
