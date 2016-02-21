<?php

namespace RulerZ\Compiler;

class EvalEvaluator implements Evaluator
{
    public function evaluate($ruleIdentifier, callable $compiler)
    {
        eval($compiler());
    }
}
