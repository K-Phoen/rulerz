<?php

namespace spec\RulerZ\Context;

use PhpSpec\ObjectBehavior;

class ArrayContextSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'foo' => 'bar',
        ]);
        $this->shouldImplement('ArrayAccess');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Context\ArrayContext');
    }

    function it_allows_property_access_through_offset($object)
    {
        $this['foo']->shouldReturn('bar');
    }

    function it_allows_testing_property_existence($object)
    {
        $this->offsetExists('foo')->shouldReturn(true);
        $this->offsetExists('non_existent_property')->shouldReturn(false);
    }

    function it_forbids_setting_properties()
    {
        $this->shouldThrow('\RuntimeException')->duringOffsetSet('bar', 'baz');
    }

    function it_forbids_unsetting_properties()
    {
        $this->shouldThrow('\RuntimeException')->duringOffsetUnset('foo');
    }
}
