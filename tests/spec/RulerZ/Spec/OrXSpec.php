<?php

declare(strict_types=1);

namespace spec\RulerZ\Spec;

use PhpSpec\ObjectBehavior;

use RulerZ\Exception\ParameterOverridenException;
use RulerZ\Spec\OrX;
use RulerZ\Spec\Specification;

class OrXSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(OrX::class);
    }

    public function it_can_be_initialized_with_specs(Specification $spec, Specification $otherSpec)
    {
        $this->beConstructedWith([$spec, $otherSpec]);
    }

    public function it_builds_the_rule_by_aggregating_the_specifications(Specification $spec, Specification $otherSpec)
    {
        $spec->getRule()->willReturn('foo');
        $otherSpec->getRule()->willReturn('bar');

        $this->beConstructedWith([$spec, $otherSpec]);

        $this->getRule()->shouldReturn('(foo) OR (bar)');
    }

    public function it_builds_the_rule_by_correctly_aggregating_the_specifications(Specification $spec, Specification $otherSpec)
    {
        $spec->getRule()->willReturn('foo AND baz');
        $otherSpec->getRule()->willReturn('bar');

        $this->beConstructedWith([$spec, $otherSpec]);

        $this->getRule()->shouldReturn('(foo AND baz) OR (bar)');
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
