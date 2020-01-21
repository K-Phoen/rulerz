<?php

namespace RulerZ\Target\Operators;

class OperatorTools
{
    public static function inlineMixedInstructions(array $instructions, $operator = null, $useStringBreakingLogic = true)
    {
        $elements = [];

        foreach ($instructions as $instruction) {
            if ($instruction instanceof RuntimeOperator) {
                $elements[] = $instruction->format($useStringBreakingLogic);
            } else if ($instruction instanceof CompileTimeOperator) {
                $elements[] = sprintf('%s', $instruction->format(false));
            } else {
                $elements[] = sprintf('%s', $instruction);
            }
        }

        if (null === $operator) {
            return join('', $elements);
        } else {
            return join(' '.$operator.' ', $elements);
        }
    }
}
