<?php

namespace RulerZ\Executor\DoctrineQueryBuilder;

use Doctrine\ORM\QueryBuilder;

trait AutoJoinTrait
{
    /**
     * @var AutoJoin
     */
    private $autoJoin;

    private function getJoinAlias(QueryBuilder $queryBuilder, $table)
    {
        if ($this->autoJoin === null) {
            $this->autoJoin = new AutoJoin($queryBuilder, $this->detectedJoins);
        }

        return $this->autoJoin->getJoinAlias($table);
    }
}
