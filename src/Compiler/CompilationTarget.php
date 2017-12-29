<?php

namespace RulerZ\Compiler;

use RulerZ\Model\Rule;

interface CompilationTarget
{
    const MODE_FILTER = 'filter';

    const MODE_APPLY_FILTER = 'apply_filter';

    const MODE_SATISFIES = 'satisfies';

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
     * @param string $mode The execution mode (see MODE_* constants).
     *
     * @return bool
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

    /**
     * Returns a hint that will be used to make the rule identifying process more
     * accurate.
     *
     * @param string $rule The textual rule.
     * @param Context $context The compilation context.
     *
     * @return string The hint (empty string if not relevant).
     */
    public function getRuleIdentifierHint($rule, Context $context);

    /**
     * Define a runtime operator.
     *
     * @param string $name The operator name.
     * @param callable $transformer The operator implementation (will be called at runtime when the operator is used).
     */
    public function defineOperator($name, callable $transformer);

    /**
     * Define a compile-time operator.
     *
     * @param string $name The operator name.
     * @param callable $transformer The operator implementation (will be called at compile-time when the operator is used).
     */
    public function defineInlineOperator($name, callable $transformer);

    /**
     * @return \RulerZ\Target\Operators\Definitions
     */
    public function getOperators();
}
