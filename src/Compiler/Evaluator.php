<?php

declare(strict_types=1);

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
     */
    public function evaluate(string $ruleIdentifier, callable $compiler): void;
}
