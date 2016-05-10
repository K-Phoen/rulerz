<?php

namespace RulerZ\Compiler;

/**
 * Evaluates PHP code using the `eval` function.
 *
 * Usage:
 *
 * ```
 * $evaluator = new EvalEvaluator();
 * $evaluator->evaluate('some-rule-unique-identifier', function() {
 *     return "echo 'Hello World';";
 * });
 * ```
 */
class EvalEvaluator implements Evaluator
{
    public function evaluate($ruleIdentifier, callable $compiler)
    {
        eval($compiler());
    }
}
