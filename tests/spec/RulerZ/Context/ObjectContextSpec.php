<?php

namespace spec\RulerZ\Context;

use PhpSpec\ObjectBehavior;

class ObjectContextSpec extends ObjectBehavior
{
    public function let(\stdClass $object)
    {
        $this->beConstructedWith($object);
        $this->shouldImplement('ArrayAccess');
    }
    
    public function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Context\ObjectContext');
    }

    function it_allows_property_access_through_offset($object)
    {
        $object->property = 42;

        $this['property']->shouldReturn(42);
    }
    
    public function it_doesnt_wrap_nulls_with_self($object)
    {
        $object->property = null;

        $this['property']->shouldReturn(null);
    }
    
    public function it_allows_testing_property_existence($object)
    {
        $object->property = 42;

        $this->offsetExists('property')->shouldReturn(true);
        $this->offsetExists('non_existent_property')->shouldReturn(false);
    }
    
    public function it_forbids_setting_properties()
    {
        $this->shouldThrow('\RuntimeException')->duringOffsetSet('foo', 'bar');
    }
    
    public function it_forbids_unsetting_properties()
    {
        $this->shouldThrow('\RuntimeException')->duringOffsetUnset('foo');
    }
}
