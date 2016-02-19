<?php

namespace RulerZ\Compiler;

use RulerZ\Executor\Executor;

interface Compiler
{
    /**
     * @param string $rule
     * @param Target\CompilationTarget $compilationTarget
     * @param Context $compilationContext
     *
     * @return Executor
     */
    public function compile($rule, Target\CompilationTarget $compilationTarget, Context $compilationContext);
}
