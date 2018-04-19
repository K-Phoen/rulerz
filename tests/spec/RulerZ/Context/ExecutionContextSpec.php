<?php

declare(strict_types=1);

namespace spec\RulerZ\Context;

use PhpSpec\ObjectBehavior;
use RulerZ\Context\ExecutionContext;

class ExecutionContextSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([
            'some' => 'data',
        ]);
        $this->shouldImplement(\ArrayAccess::class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ExecutionContext::class);
    }

    public function it_allows_property_access_through_offset($object)
    {
        $this['some']->shouldReturn('data');
    }

    public function it_allows_testing_property_existence($object)
    {
        $this->offsetExists('some')->shouldReturn(true);
        $this->offsetExists('non_existent_offset')->shouldReturn(false);
    }

    public function it_forbids_setting_properties()
    {
        $this->shouldThrow(\LogicException::class)->duringOffsetSet('foo', 'bar');
    }

    public function it_forbids_unsetting_properties()
    {
        $this->shouldThrow(\LogicException::class)->duringOffsetUnset('foo');
    }
}
