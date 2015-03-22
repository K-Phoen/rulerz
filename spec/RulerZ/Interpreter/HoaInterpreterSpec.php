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

    /**
     * @dataProvider validRules
     */
    function it_returns_an_ast_for_a_valid_rule($rule)
    {
        $this->interpret($rule)->shouldHaveType('Hoa\Ruler\Model');
    }

    /**
     * @dataProvider invalidRules
     */
    function it_throws_an_exception_for_an_invalid_rule($rule)
    {
        $this->shouldThrow('Hoa\Compiler\Exception')->duringInterpret($rule);
    }

    public function validRules()
    {
        return [
            [ 'points > 30' ],
            [ 'locked = false' ],
            [ 'admin = true' ],
            [ 'user.group = "members"' ],
            [ "user.group = 'members'" ],
            [ 'user.group in ["members", "admins"]' ],
            [ 'length(name) = 4' ],
            [ 'distance(lat1, long1, lat2, long2) < 50' ],
            [ 'name = :user_name' ], // should not be allowed
            [ 'name = ?' ],
            [ 'points > 30 and group = "member"' ],
            [ '(points > 30 and group in ["member", "guest"]) or group = "admin"' ],
            [ 'not points > 30' ],
        ];
    }

    public function invalidRules()
    {
        return [
            [ '> 30' ],
            [ 'name[0] = "a"' ],
            [ 'name.foo() = "a"' ],
        ];
    }
}
