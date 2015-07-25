<?php

namespace RulerZ\Executor\ArrayTarget;

use RulerZ\Context\ExecutionContext;
use RulerZ\Context\ObjectContext;

trait SatisfiesTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function satisties($target, array $parameters, array $operators, ExecutionContext $context)
    {
        $wrappedTarget = is_array($target) ? $target : new ObjectContext($target);

        return $this->execute($wrappedTarget, $operators, $parameters);
    }
}
