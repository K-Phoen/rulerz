<?php

declare(strict_types=1);

namespace spec\RulerZ\Target;

use PhpSpec\ObjectBehavior;
use RulerZ\Compiler\CompilationTarget;
use RulerZ\Model\Rule;
use RulerZ\Parser\Parser;

abstract class BaseTargetBehavior extends ObjectBehavior
{
    /**
     * @dataProvider unsupportedTypes
     */
    public function it_can_not_filter_other_types($type)
    {
        $this->supports($type, CompilationTarget::MODE_FILTER)->shouldReturn(false);
    }

    public function unsupportedTypes(): array
    {
        return [
            'string',
            42,
            new \stdClass(),
            [],
        ];
    }

    protected function parseRule(string $rule): Rule
    {
        return (new Parser())->parse($rule);
    }
}
