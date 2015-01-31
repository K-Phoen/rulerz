<?php

namespace spec\RulerZ\Interpreter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HoaInterpreterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Interpreter\HoaInterpreter');
    }

    function it_returns_an_ast_for_a_valid_rule()
    {
        $this->interpret('points > 30')->shouldHaveType('Hoa\Ruler\Model');
    }

    function it_throws_an_exception_for_an_invalid_rule()
    {
        $this->shouldThrow('Hoa\Compiler\Exception')->duringInterpret('> and');
    }
}
