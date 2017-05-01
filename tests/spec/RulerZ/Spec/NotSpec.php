<?php

namespace spec\RulerZ\Spec;

use PhpSpec\ObjectBehavior;

use RulerZ\Spec\Specification;

class NotSpec extends ObjectBehavior
{
    public function it_is_initializable(Specification $spec)
    {
        $this->beConstructedWith($spec);
        $this->shouldHaveType('RulerZ\Spec\Not');
    }

    public function it_negates_the_given_spec(Specification $spec)
    {
        $spec->getRule()->willReturn('foo');

        $this->beConstructedWith($spec);

        $this->getRule()->shouldReturn('NOT (foo)');
    }

    public function it_returns_the_parameters_of_the_given_spec(Specification $spec)
    {
        $spec->getParameters()->willReturn(['foo' => 'bar']);

        $this->beConstructedWith($spec);

        $this->getParameters()->shouldReturn([
            'foo' => 'bar',
        ]);
    }
}
