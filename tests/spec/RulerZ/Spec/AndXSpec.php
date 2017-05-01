<?php

namespace spec\RulerZ\Spec;

use PhpSpec\ObjectBehavior;

use RulerZ\Spec\Specification;

class AndXSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Spec\AndX');
    }

    public function it_can_be_initialized_with_specs(Specification $spec, Specification $otherSpec)
    {
        $this->beConstructedWith([$spec, $otherSpec]);
    }

    public function it_accepts_new_specifications_after_initialization(Specification $spec)
    {
        $this->addSpecification($spec);
    }

    public function it_builds_the_rule_by_aggregating_the_specifications(Specification $spec, Specification $otherSpec)
    {
        $spec->getRule()->willReturn('foo');
        $otherSpec->getRule()->willReturn('bar');

        $this->beConstructedWith([$spec, $otherSpec]);

        $this->getRule()->shouldReturn('(foo) AND (bar)');
    }

    public function it_builds_the_rule_by_correctly_aggregating_the_specifications(Specification $spec, Specification $otherSpec)
    {
        $spec->getRule()->willReturn('foo OR baz');
        $otherSpec->getRule()->willReturn('bar');

        $this->beConstructedWith([$spec, $otherSpec]);

        $this->getRule()->shouldReturn('(foo OR baz) AND (bar)');
    }

    public function it_builds_the_parameters_by_aggregating_the_specifications(Specification $spec, Specification $otherSpec)
    {
        $spec->getParameters()->willReturn(['foo' => 'bar']);
        $otherSpec->getParameters()->willReturn(['bar' => 'baz']);

        $this->beConstructedWith([$spec, $otherSpec]);

        $this->getParameters()->shouldReturn([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);
    }

    public function it_detects_parameter_collisions(Specification $spec, Specification $otherSpec)
    {
        $spec->getParameters()->willReturn([
            'foo' => 'bar',
        ]);
        $otherSpec->getParameters()->willReturn([
            'foo' => 'baz',
        ]);

        $this->beConstructedWith([$spec, $otherSpec]);

        $this
            ->shouldThrow('RulerZ\Exception\ParameterOverridenException')
            ->duringGetParameters();
    }
}
