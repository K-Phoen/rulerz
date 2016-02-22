<?php

namespace RulerZ\Compiler;

use RulerZ\Model\Rule;

interface CompilationTarget
{
    const MODE_FILTER       = 'filter';
    const MODE_APPLY_FILTER = 'apply_filter';
    const MODE_SATISFIES    = 'satisfies';

    /**
     * Compiles the given rule.
     *
     * @param Rule $rule The rule.
     * @param Context $compilationContext The compilation context.
     *
     * @return \RulerZ\Model\Executor
     */
    public function compile(Rule $rule, Context $compilationContext);

    /**
     * Indicates whether the given target is supported or not.
     *
     * @param mixed  $target The target to test.
     * @param string $mode The execution mode (MODE_FILTER or MODE_SATISFIES).
     *
     * @return boolean
     */
    public function supports($target, $mode);

    /**
     * Create a compilation context from a sample target.
     *
     * @param mixed $target The target.
     *
     * @return Context
     */
    public function createCompilationContext($target);

    public function defineOperator($name, callable $transformer);
    public function defineInlineOperator($name, callable $transformer);

    /**
     * @return \RulerZ\Target\Operators\Definitions
     */
    public function getOperators();
}
