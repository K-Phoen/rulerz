<?php

namespace RulerZ\Compiler\Target;

use RulerZ\Model\Rule;

/**
 * Represents a compilation target.
 */
interface CompilationTarget
{
    const MODE_FILTER       = 'filter';
    const MODE_APPLY_FILTER = 'apply_filter';
    const MODE_SATISFIES    = 'satisfies';

    /**
     * Compiles the given rule.
     *
     * @param Rule $rule The rule.
     *
     * @return \RulerZ\Model\Executor
     */
    public function compile(Rule $rule);

    /**
     * Indicates whether the given target is supported or not.
     *
     * @param mixed  $target The target to test.
     * @param string $mode   The execution mode (MODE_FILTER or MODE_SATISFIES).
     *
     * @return boolean
     */
    public function supports($target, $mode);

    /**
     * Get the operators list.
     *
     * @return array<Xcallable>
     */
    public function getOperators();
}
