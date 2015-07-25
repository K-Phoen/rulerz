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
     * PHP code used to initialize the rule execution.
     *
     * @var string
     */
    private $initializationCode = '';

    /**
     * Compiled code of the rule.
     *
     * @var string
     */
    private $compiledRule = '';

    public function __construct(array $traits, $initializationCode, $compiledRule)
    {
        $this->traits             = $traits;
        $this->initializationCode = $initializationCode;
        $this->compiledRule       = $compiledRule;
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
    public function getInitializationCode()
    {
        return $this->initializationCode;
    }

    /**
     * @return array
     */
    public function getCompiledRule()
    {
        return $this->compiledRule;
    }
}
