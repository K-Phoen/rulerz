<?php

declare(strict_types=1);

namespace RulerZ\Executor\ArrayTarget;

use RulerZ\Context\ExecutionContext;
use RulerZ\Context\ObjectContext;

trait SatisfiesTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritdoc}
     */
    public function satisfies($target, array $parameters, array $operators, ExecutionContext $context): bool
    {
        $wrappedTarget = is_array($target) ? $target : new ObjectContext($target);

        return (bool) $this->execute($wrappedTarget, $operators, $parameters);
    }
}
