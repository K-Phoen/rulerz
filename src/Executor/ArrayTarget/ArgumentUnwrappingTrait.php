<?php

declare(strict_types=1);

namespace RulerZ\Executor\ArrayTarget;

use RulerZ\Context\ObjectContext;

trait ArgumentUnwrappingTrait
{
    private function unwrapArgument($argument)
    {
        return $argument instanceof ObjectContext ? $argument->getObject() : $argument;
    }
}
