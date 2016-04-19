<?php

namespace RulerZ\Compiler;

interface Evaluator
{
    /**
     * Evaluates the executor corresponding to the given rule identifier.
     *
     * @param string $ruleIdentifier A unique rule identifier.
     * @param callable $compiler Callable used to build the source-code for a
     *                           rule if it can not be evaluated from a cache.
     *
     * @note Evaluators are only called if the Executor does not already exists.
     *
     * @return void
     */
    public function evaluate($ruleIdentifier, callable $compiler);
}
