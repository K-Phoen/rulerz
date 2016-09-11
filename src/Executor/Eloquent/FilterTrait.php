<?php

namespace RulerZ\Executor\Eloquent;

use Illuminate\Database\Query\Builder as QueryBuilder;

use RulerZ\Context\ExecutionContext;
use RulerZ\Result\IteratorTools;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function applyFilter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var QueryBuilder $query */
        $query = !$target instanceof QueryBuilder ? $target->getQuery() : $target;
        $sql   = $this->execute($target, $operators, $parameters);

        $query->whereRaw($sql, $parameters);

        return $query;
    }

    /**
     * @inheritDoc
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        $query = $this->applyFilter($target, $parameters, $operators, $context);

        return IteratorTools::ensureTraversable($query->get());
    }
}
