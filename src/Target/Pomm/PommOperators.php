<?php

namespace RulerZ\Target\Pomm;

use RulerZ\Target\Operators;

class PommOperators
{
    /**
     * @return Operators\Definitions
     */
    public static function create(Operators\Definitions $customOperators)
    {
        $definitions = Operators\GenericSqlDefinitions::create($customOperators);

        $definitions->defineInlineOperator('and', function ($a, $b) {
            return sprintf('%s->andWhere(%s)', $a, $b);
        });
        $definitions->defineInlineOperator('or', function ($a, $b) {
            return sprintf('%s->orWhere(%s)', $a, $b);
        });
        $definitions->defineInlineOperator('not', function ($a) {
            return sprintf('(new \PommProject\Foundation\Where("NOT(".%s->getElement() .")", %s->getValues()))', $a, $a);
        });

        return $definitions;
    }
}
