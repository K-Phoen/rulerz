<?php

namespace RulerZ\Executor\Pomm;

use RulerZ\Context\ExecutionContext;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function applyFilter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        return $this->execute($target, $operators, $parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var \PommProject\Foundation\Where $whereClause */
        $whereClause = $this->applyFilter($target, $parameters, $operators, $context);
        $method      = !empty($context['method']) ? $context['method'] : 'findWhere';

        return call_user_func([$target, $method], $whereClause);
    }
}
