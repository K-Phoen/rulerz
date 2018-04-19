<?php

declare(strict_types=1);

namespace RulerZ\Compiler;

use RulerZ\Model\Executor;
use RulerZ\Model\Rule;
use RulerZ\Target\Operators\Definitions;

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
     */
    public function compile(Rule $rule, Context $compilationContext): Executor;

    /**
     * Indicates whether the given target is supported or not.
     *
     * @param mixed  $target The target to test.
     * @param string $mode The execution mode (see MODE_* constants).
     */
    public function supports($target, string $mode): bool;

    /**
     * Create a compilation context from a sample target.
     *
     * @param mixed $target The target.
     */
    public function createCompilationContext($target): Context;

    /**
     * Returns a hint that will be used to make the rule identifying process more
     * accurate.
     *
     * @param Context $context The compilation context.
     *
     * @return string The hint (empty string if not relevant).
     */
    public function getRuleIdentifierHint(string $rule, Context $context): string;

    /**
     * Define a runtime operator.
     *
     * @param callable $transformer The operator implementation (will be called at runtime when the operator is used).
     */
    public function defineOperator(string $name, callable $transformer): void;

    /**
     * Define a compile-time operator.
     *
     * @param callable $transformer The operator implementation (will be called at compile-time when the operator is used).
     */
    public function defineInlineOperator(string $name, callable $transformer): void;

    public function getOperators(): Definitions;
}
