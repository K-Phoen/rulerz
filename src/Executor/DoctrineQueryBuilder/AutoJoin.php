<?php

namespace RulerZ\Executor\DoctrineQueryBuilder;

use Doctrine\ORM\QueryBuilder;

class AutoJoin
{
    const ALIAS_PREFIX = 'rulerz_';

    /**
     * Associative list of known aliases (selected or joined tables).
     *
     * @var array
     */
    private $knownAliases = [];

    /**
     * Associative list of joined tables and their alias.
     *
     * @var array
     */
    private $joinMap = null;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var array
     */
    private $expectedJoinChains = [];

    public function __construct(QueryBuilder $queryBuilder, array $expectedJoinChains)
    {
        $this->queryBuilder       = $queryBuilder;
        $this->expectedJoinChains = $expectedJoinChains;
    }

    public function getJoinAlias($table)
    {
        if ($this->joinMap === null) {
            $this->joinMap      = $this->analizeJoinedTables($this->queryBuilder);
            $this->knownAliases = array_flip($this->queryBuilder->getRootAliases()) + array_flip($this->joinMap);

            $this->autoJoin($this->queryBuilder);
        }

        // the table name is a known alias (already join for instance) so we
        // don't need to do anything.
        if (isset($this->knownAliases[$table])) {
            return $table;
        }

        // otherwise the table should have automatically been joined, so we use our table prefix
        if (isset($this->knownAliases[self::ALIAS_PREFIX.$table])) {
            return self::ALIAS_PREFIX . $table;
        }

        throw new \RuntimeException(sprintf('Could not automatically join table "%s"', $table));
    }

    /**
     * Builds an associative array of already joined tables and their alias.
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return array
     */
    private function analizeJoinedTables(QueryBuilder $queryBuilder)
    {
        $joinMap = [];
        $joins   = $queryBuilder->getDQLPart('join');
        foreach (array_keys($joins) as $fromTable) {
            foreach ($joins[$fromTable] as $join) {
                $joinMap[$join->getJoin()] = $join->getAlias();
            }
        }
        return $joinMap;
    }

    private function autoJoin(QueryBuilder $queryBuilder)
    {
        foreach ($this->expectedJoinChains as $tablesToJoin) {
            // check if the first dimension is a known alias
            if (isset($this->knownAliases[$tablesToJoin[0]])) {
                $joinTo = $tablesToJoin[0];
                array_pop($tablesToJoin);
            } else { // if not, it's the root table
                $joinTo = $queryBuilder->getRootAliases()[0];
            }

            foreach ($tablesToJoin as $table) {
                $joinAlias = self::ALIAS_PREFIX . $table;
                $join      = sprintf('%s.%s', $joinTo, $table);

                if (!isset($this->joinMap[$join])) {
                    $this->joinMap[$join]           = $joinAlias;
                    $this->knownAliases[$joinAlias] = true;

                    $queryBuilder->join(sprintf('%s.%s', $joinTo, $table), $joinAlias);
                } else {
                    $joinAlias = $this->joinMap[$join];
                }

                $joinTo = $joinAlias;
            }
        }
    }
}
