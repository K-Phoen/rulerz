<?php

namespace spec\RulerZ\Context;

use PhpSpec\ObjectBehavior;
use RulerZ\Context\ObjectContext;

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

    public function it_allows_property_access_through_offset($object)
    {
        $object->property = 42;

        $this['property']->shouldReturn(42);
    }

    public function it_doesnt_wrap_nulls_with_self($object)
    {
        $object->property = null;

        $this['property']->shouldReturn(null);
    }

    public function it_should_return_a_new_ObjectContext_when_we_dont_specify_a_property_of_an_object($object)
    {
        $object->property = new \stdClass();

        $this['property']->shouldHaveType(ObjectContext::class);
    }

    public function it_should_return_a_Datetime_object_whithout_an_encapuslation_of_ObjectContext($object)
    {
        $object->property = new \DateTime();

        $this['property']->shouldHaveType(\DateTime::class);
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
