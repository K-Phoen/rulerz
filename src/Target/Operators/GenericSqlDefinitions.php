<?php

declare(strict_types=1);

namespace RulerZ\Target\Operators;

class GenericSqlDefinitions
{
    public static function create(Definitions $customOperators): Definitions
    {
        $defaultInlineOperators = [
            'and' => function ($a, $b) {
                return sprintf('(%s AND %s)', $a, $b);
            },
            'or' => function ($a, $b) {
                return sprintf('(%s OR %s)', $a, $b);
            },
            'not' => function ($a) {
                return sprintf('NOT (%s)', $a);
            },
            '=' => function ($a, $b) {
                return sprintf('%s = %s', $a, $b);
            },
            '!=' => function ($a, $b) {
                return sprintf('%s != %s', $a, $b);
            },
            '>' => function ($a, $b) {
                return sprintf('%s > %s', $a, $b);
            },
            '>=' => function ($a, $b) {
                return sprintf('%s >= %s', $a, $b);
            },
            '<' => function ($a, $b) {
                return sprintf('%s < %s', $a, $b);
            },
            '<=' => function ($a, $b) {
                return sprintf('%s <= %s', $a, $b);
            },
            'in' => function ($a, $b) {
                return sprintf('%s IN %s', $a, $b[0] === '(' ? $b : '('.$b.')');
            },
            'like' => function ($a, $b) {
                return sprintf('%s LIKE %s', $a, $b);
            },
        ];

        $definitions = new Definitions();
        $definitions->defineInlineOperators($defaultInlineOperators);

        return $definitions->mergeWith($customOperators);
    }
}
