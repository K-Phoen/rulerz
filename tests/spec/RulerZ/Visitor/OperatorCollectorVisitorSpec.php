<?php

namespace spec\RulerZ\Visitor;

use PhpSpec\ObjectBehavior;
use RulerZ\Model\Rule;
use RulerZ\Parser\Parser;
use RulerZ\Visitor\OperatorCollectorVisitor;

class OperatorCollectorVisitorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(OperatorCollectorVisitor::class);
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

    private function parse(string $rule): Rule
    {
        return (new Parser())->parse($rule);
    }
}
