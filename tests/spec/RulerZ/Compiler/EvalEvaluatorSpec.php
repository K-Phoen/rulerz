<?php

namespace spec\RulerZ\Compiler;

use PhpSpec\ObjectBehavior;
use RulerZ\Compiler\EvalEvaluator;
use RulerZ\Compiler\Evaluator;

class EvalEvaluatorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(EvalEvaluator::class);
        $this->shouldHaveType(Evaluator::class);
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

    public function getMatchers(): array
    {
        return [
            'haveLoaded' => function ($subject, $class): bool {
                return class_exists($class, false);
            },
        ];
    }
}
