<?php

namespace RulerZ\Target\Operators;

class GenericSqlDefinitions
{
    /**
     * @return Definitions
     */
    public static function create(Definitions $customOperators)
    {
        $defaultInlineOperators = [
            'and' => function ($a, $b) {
                return sprintf('(%s)', OperatorTools::inlineMixedInstructions([$a, $b], 'AND'));
            },
            'or' => function ($a, $b) {
                return sprintf('(%s)', OperatorTools::inlineMixedInstructions([$a, $b], 'OR'));
            },
            'not' => function ($a) {
                return sprintf('NOT (%s)', OperatorTools::inlineMixedInstructions([$a]));
            },
            '=' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '=');
            },
            '!=' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '!=');
            },
            '>' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '>');
            },
            '>=' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '>=');
            },
            '<' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '<');
            },
            '<=' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], '<=');
            },
            'in' => function ($a, $b) {
                if ($b[0] === '(') {
                    return OperatorTools::inlineMixedInstructions([$a, $b], 'IN');
                } else {
                    return sprintf(
                        '%s IN (%s)',
                        OperatorTools::inlineMixedInstructions([$a]),
                        OperatorTools::inlineMixedInstructions([$b])
                    );
                }
            },
            'like' => function ($a, $b) {
                return OperatorTools::inlineMixedInstructions([$a, $b], 'LIKE');
            },
        ];

        $definitions = new Definitions();
        $definitions->defineInlineOperators($defaultInlineOperators);

        return $definitions->mergeWith($customOperators);
    }
}
