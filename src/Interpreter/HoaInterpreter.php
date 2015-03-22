<?php

namespace RulerZ\Interpreter;

use Hoa\Compiler;
use Hoa\File;
use Hoa\Ruler\Visitor\Interpreter as RulerInterpreter;

/**
 * Interpretes a rule.
 */
class HoaInterpreter implements Interpreter
{
    /**
     * Compiler.
     *
     * @var \Hoa\Compiler\Llk\Parser $compiler
     */
    private $compiler;

    /**
     * Interpreter.
     *
     * @var \Hoa\Ruler\Visitor\Interpreter $Interpreter
     */
    private $interpreter;

    public function __construct()
    {
        $this->compiler = Compiler\Llk::load(
            new File\Read(__DIR__ .'/../Grammar.pp')
        );
        $this->interpreter = new RulerInterpreter();
    }

    /**
     * {@inheritDoc}
     */
    public function interpret($rule)
    {
        return $this->interpreter->visit($this->compiler->parse($rule));
    }
}
