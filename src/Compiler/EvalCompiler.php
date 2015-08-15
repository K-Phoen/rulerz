<?php

namespace RulerZ\Compiler;

class EvalCompiler extends AbstractCompiler
{
    public function compile($rule, Target\CompilationTarget $target)
    {
        $ruleIdentifier = $this->getRuleIdentifier($target, $rule);
        $executorFqcn   = '\RulerZ\Compiled\Executor\\Executor_' . $ruleIdentifier;

        if (!class_exists($executorFqcn, false)) {
            $source = $this->compileToSource($rule, $target, [
                'className' => 'Executor_' . $ruleIdentifier
            ]);
            echo $source;
            eval($source);
        }

        return new $executorFqcn();
    }
}
