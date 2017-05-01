<?php

namespace spec\RulerZ\Compiler;

use PhpSpec\ObjectBehavior;

class EvalEvaluatorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\Compiler\EvalEvaluator');
        $this->shouldHaveType('RulerZ\Compiler\Evaluator');
    }

    public function it_can_evaluate_a_rule()
    {
        $ruleIdentifier = '(irrelevant for this evaluator)';
        $compilerCallable = function () {
            return 'class WowExecutor {}';
        };

        $this->evaluate($ruleIdentifier, $compilerCallable);
        $this->shouldHaveLoaded('WowExecutor');
    }

    public function getMatchers()
    {
        return [
            'haveLoaded' => function ($subject, $class) {
                return class_exists($class, false);
            },
        ];
    }
}
