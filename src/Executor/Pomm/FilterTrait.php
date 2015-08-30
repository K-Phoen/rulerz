<?php

namespace RulerZ\Executor\Pomm;

use RulerZ\Context\ExecutionContext;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var \PommProject\Foundation\Where $whereClause */
        $whereClause = $this->execute($target, $operators, $parameters);
        $method      = !empty($context['method']) ? $context['method'] : 'findWhere';

        return call_user_func([$target, $method], $whereClause);
    }
}
