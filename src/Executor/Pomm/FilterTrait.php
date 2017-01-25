<?php

namespace RulerZ\Executor\Pomm;

use RulerZ\Context\ExecutionContext;
use RulerZ\Result\IteratorTools;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritdoc}
     */
    public function applyFilter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        return $this->execute($target, $operators, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var \PommProject\Foundation\Where $whereClause */
        $whereClause = $this->applyFilter($target, $parameters, $operators, $context);
        $method = !empty($context['method']) ? $context['method'] : 'findWhere';
        $result = call_user_func([$target, $method], $whereClause);

        return is_array($result) ? IteratorTools::fromArray($result) : $result;
    }
}
