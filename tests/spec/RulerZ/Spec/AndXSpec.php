<?php

declare(strict_types=1);

namespace spec\RulerZ\Spec;

use PhpSpec\ObjectBehavior;

use RulerZ\Exception\ParameterOverridenException;
use RulerZ\Spec\AndX;
use RulerZ\Spec\Specification;

class AndXSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(AndX::class);
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

    public function it_returns_no_parameters_if_base_specifications_dont_have_any(Specification $spec, Specification $otherSpec)
    {
        $spec->getParameters()->willReturn([]);
        $otherSpec->getParameters()->willReturn([]);

        $this->beConstructedWith([$spec, $otherSpec]);

        $this->getParameters()->shouldReturn([]);
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
            ->shouldThrow(ParameterOverridenException::class)
            ->duringGetParameters();
    }
}
