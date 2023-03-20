<?php

namespace RulerZ\Target\Operators;

class CompileTimeOperator
{
    /**
     * @var string
     */
    private $compiledOperator;

    public function __construct($compiled)
    {
        $this->compiledOperator = $compiled;
    }

    public function format($shouldBreakString)
    {
        return $this->compiledOperator;
    }
}