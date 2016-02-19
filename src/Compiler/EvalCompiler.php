<?php

namespace RulerZ\Compiler;

class EvalCompiler extends AbstractCompiler
{
    public function compile($rule, Target\CompilationTarget $target, Context $context)
    {
        $ruleIdentifier = $this->getRuleIdentifier($target, $rule);
        $executorFqcn   = '\RulerZ\Compiled\Executor\\Executor_' . $ruleIdentifier;

        if (!class_exists($executorFqcn, false)) {
            $source = $this->compileToSource($rule, $target, $context, [
                'className' => 'Executor_' . $ruleIdentifier
            ]);
            echo $source;
            eval($source);
        }

        return new $executorFqcn();
    }
}
