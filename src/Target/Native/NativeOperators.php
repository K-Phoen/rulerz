<?php

namespace RulerZ\Target\Native;

use RulerZ\Target\Operators\Definitions;

class NativeOperators
{
    /**
     * @return Definitions
     */
    public static function create(Definitions $customOperators)
    {
        $defaultInlineOperators = [
            'and' => function ($a, $b) { return sprintf('(%s && %s)', $a, $b); },
            'or' =>  function ($a, $b) { return sprintf('(%s || %s)', $a, $b); },
            'not' => function ($a)     { return sprintf('!(%s)', $a); },
            '=' =>   function ($a, $b) { return sprintf('%s == %s', $a, $b); },
            'is' =>  function ($a, $b) { return sprintf('%s === %s', $a, $b); },
            '!=' =>  function ($a, $b) { return sprintf('%s != %s', $a, $b); },
            '>' =>   function ($a, $b) { return sprintf('%s > %s', $a, $b); },
            '>=' =>  function ($a, $b) { return sprintf('%s >= %s', $a, $b); },
            '<' =>   function ($a, $b) { return sprintf('%s < %s', $a, $b); },
            '<=' =>  function ($a, $b) { return sprintf('%s <= %s', $a, $b); },
            'in' =>  function ($a, $b) { return sprintf('in_array(%s, %s)', $a, $b); },
        ];

        $defaultOperators = [
            'sum' => function () { return array_sum(func_get_args()); }
        ];

        $definitions = new Definitions($defaultOperators, $defaultInlineOperators);

        return $definitions->mergeWith($customOperators);
    }
}
