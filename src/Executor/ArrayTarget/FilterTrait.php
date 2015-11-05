<?php

namespace RulerZ\Executor\ArrayTarget;

use RulerZ\Context\ExecutionContext;
use RulerZ\Context\ObjectContext;
use RulerZ\Result\FilterResult;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        return FilterResult::fromGenerator(function() use ($target, $parameters, $operators) {
            foreach ($target as $row) {
                $targetRow = is_array($row) ? $row : new ObjectContext($row);

                if ($this->execute($targetRow, $operators, $parameters)) {
                    yield $row;
                }
            }
        });
    }
}
