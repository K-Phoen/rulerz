<?php

namespace spec\RulerZ\Context;

use PhpSpec\ObjectBehavior;

class ExecutionContextSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'some' => 'data',
        ]);
        $this->shouldImplement('ArrayAccess');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Context\ExecutionContext');
    }

    function it_allows_property_access_through_offset($object)
    {
        $this['some']->shouldReturn('data');
    }

    function it_allows_testing_property_existence($object)
    {
        $this->offsetExists('some')->shouldReturn(true);
        $this->offsetExists('non_existent_offset')->shouldReturn(false);
    }

    function it_forbids_setting_properties()
    {
        $this->shouldThrow('\LogicException')->duringOffsetSet('foo', 'bar');
    }

    function it_forbids_unsetting_properties()
    {
        $this->shouldThrow('\LogicException')->duringOffsetUnset('foo');
    }
}
