<?php

namespace RulerZ\Target\Native;

use RulerZ\Target\Operators\Definitions;
use RulerZ\Target\Operators\OperatorTools;

class NativeOperators
{
    /**
     * @return Definitions
     */
    public static function create(Definitions $customOperators)
    {
        $defaultInlineOperators = [
            'and' => function ($a, $b) {
                return sprintf('(%s)', OperatorTools::inlineMixedInstructions([$a, $b], '&&', false));
            },
            'or' => function ($a, $b) {
                return sprintf('(%s)', OperatorTools::inlineMixedInstructions([$a, $b], '||', false));
            },
            'not' => function ($a) {
                return sprintf('!(%s)', OperatorTools::inlineMixedInstructions([$a], null, false));
            },
            '=' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '==', false);
            },
            'is' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '===', false);
            },
            '!=' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '!=', false);
            },
            '>' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '>', false);
            },
            '>=' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '>=', false);
            },
            '<' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '<', false);
            },
            '<=' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '<=', false);
            },
            'in' => function ($a, $b) {
                return sprintf('in_array(%s)', OperatorTools::inlineMixedInstructions([$a, $b], ',', false));
            },
        ];

        $defaultOperators = [
            'sum' => function () {
                return array_sum(func_get_args());
            },
        ];

        $definitions = new Definitions($defaultOperators, $defaultInlineOperators);

        return $definitions->mergeWith($customOperators);
    }
}
