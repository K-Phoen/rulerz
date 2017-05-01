<?php

namespace spec\RulerZ\Visitor;

use PhpSpec\ObjectBehavior;
use RulerZ\Parser\Parser;

class OperatorCollectorVisitorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Visitor\OperatorCollectorVisitor');
    }

    public function it_collects_operators()
    {
        $ruleModel = $this->parse('foo = :bar AND length(baz) = 2');

        $operators = $this->visit($ruleModel);

        $operators->shouldBeArray();
        $operators->shouldHaveCount(4);

        $this->getCompilationData()->shouldBeArray();
        $this->getCompilationData()->shouldHaveCount(1);
        $this->getCompilationData()->shouldHaveKey('operators');

        $compilationData = $this->getCompilationData();
        $compilationData['operators']->shouldBeArray();
        $compilationData['operators']->shouldHaveCount(4);
    }

    private function parse($rule)
    {
        return (new Parser())->parse($rule);
    }
}
