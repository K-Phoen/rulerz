<?php

namespace Executor;

use Doctrine\ORM\QueryBuilder;
use Hoa\Ruler\Model;

use Visitor\DoctrineQueryBuilderVisitor;

class DoctrineQueryBuilderExecutor implements Executor
{
    public function filter(Model $rule, $target)
    {
        $target->andWhere($this->buildWhereClause($rule, $target));

        return $target->getQuery()->getResult();
    }

    public function supports($target)
    {
        return $target instanceof QueryBuilder;
    }

    private function buildWhereClause(Model $rule, $target)
    {
        $dqlBuilder = new DoctrineQueryBuilderVisitor($target);

        return $dqlBuilder->visit($rule);
    }
}
