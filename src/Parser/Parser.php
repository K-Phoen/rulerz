<?php

namespace RulerZ\Parser;

/**
 * Buils an AST from a rule.
 */
interface Parser
{
    /**
     * Parses the rule into an equivalent AST.
     *
     * @param string $rule The rule represented as a string.
     *
     * @return \RulerZ\Model\Rule
     */
    public function parse($rule);
}
