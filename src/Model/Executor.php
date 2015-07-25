<?php

namespace RulerZ\Model;

use Hoa\Ruler\Model as HoaModel;

class Executor
{
    /**
     * List of the traits to use in the executor's code.
     *
     * @var array
     */
    private $traits = [];

    /**
     * Compiled code of the rule.
     *
     * @var string
     */
    private $compiledRule = '';

    public function __construct(array $traits, $compiledRule)
    {
        $this->traits       = $traits;
        $this->compiledRule = $compiledRule;
    }

    /**
     * @return array
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * @return array
     */
    public function getCompiledRule()
    {
        return $this->compiledRule;
    }
}
