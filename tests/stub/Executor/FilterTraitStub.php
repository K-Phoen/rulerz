<?php

namespace RulerZ\Stub\Executor;

use RulerZ\Context\ExecutionContext;

trait FilterTraitStub
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function applyFilter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        throw new \LogicException('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        return $this->execute($target, $operators, $parameters);
    }
}
