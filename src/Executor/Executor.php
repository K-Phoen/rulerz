<?php

namespace Executor;

use Hoa\Ruler\Model;

/**
 * An executor executes a rule against a target.
 */
interface Executor
{
    /**
     * Filters a target using the given rule and parameters.
     *
     * @param mixed $target     The target to filter.
     * @param Model $rule       The rule to apply.
     * @param array $parameters The parameters used in the rule.
     *
     * @return mixed The filtered target.
     */
    public function filter($target, Model $rule, array $parameters = []);

    /**
     * Indicates whether the given object can be filtered by the executor.
     *
     * @param mixed $target The target to test.
     *
     * @return boolean
     */
    public function supports($target);
}
