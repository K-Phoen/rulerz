<?php

namespace RulerZ\Executor\Eloquent;

use Illuminate\Database\Query\Builder as QueryBuilder;

use RulerZ\Context\ExecutionContext;
use RulerZ\Result\FilterResult;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * @inheritDoc
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        $query = !$target instanceof QueryBuilder ? $target->getQuery() : $target;
        $sql   = $this->execute($target, $operators, $parameters);

        $query->whereRaw($sql, $parameters);

        return FilterResult::fromArray($query->get());
    }
}
