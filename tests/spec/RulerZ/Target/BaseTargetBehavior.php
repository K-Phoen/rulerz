<?php

namespace spec\RulerZ\Target;

use PhpSpec\ObjectBehavior;
use RulerZ\Compiler\CompilationTarget;
use RulerZ\Parser\Parser;

abstract class BaseTargetBehavior extends ObjectBehavior
{
    /**
     * @dataProvider unsupportedTypes
     */
    function it_can_not_filter_other_types($type)
    {
        $this->supports($type, CompilationTarget::MODE_FILTER)->shouldReturn(false);
    }

    public function unsupportedTypes()
    {
        return [
            'string',
            42,
            new \stdClass(),
            [],
        ];
    }

    protected function parseRule($rule)
    {
        return (new Parser())->parse($rule);
    }
}
