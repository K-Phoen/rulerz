<?php

namespace RulerZ\Executor\DoctrineQueryBuilder;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;

class AutoJoin
{
    const ALIAS_PREFIX = 'rulerz_';

    /**
     * List of root and association entity embeddables
     *
     * @var array
     */
    private $embeddables = null;

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
        if ($this->embeddables === null) {
            $this->embeddables = $this->analizeEmbeddables($this->queryBuilder);
        }

        if ($this->joinMap === null) {
            $this->joinMap      = $this->analizeJoinedTables($this->queryBuilder);
            $this->knownAliases = array_flip($this->queryBuilder->getRootAliases()) + array_flip($this->joinMap);

            $this->autoJoin($this->queryBuilder);
        }

        // the table is identified as an embeddable
        if (array_search($table, $this->embeddables) !== false) {
            return $this->getEmbeddableAlias($table);
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

    private function getEmbeddableAlias($table)
    {
        $embeddable_dimensions = explode('.', $table);

        if (count($embeddable_dimensions) === 1) {
            // the embeddable is not inside an association, so we use the root alias prefix
            $embeddable_alias = $this->queryBuilder->getRootAliases()[0] . '.' . $table;
        }
        else {
            // remove the embeddable's property
            array_pop($embeddable_dimensions);
            $table_alias = implode('.', $embeddable_dimensions);

            if (isset($this->knownAliases[$table_alias])) {
                // the table name is a known alias (already join for instance) so we
                // don't need to do anything.
                $embeddable_alias = $table;
            } else {
                // otherwise the table should have automatically been joined, so we use our table prefix
                $embeddable_alias = self::ALIAS_PREFIX . $table;
            }
        }

        return $embeddable_alias;
    }

    private function traverseAssociationsForEmbeddables(EntityManager $entityManager, array $associations, $fieldNamePrefix = false)
    {
        $associationsEmbeddables = array();

        foreach ($associations as $association) {
            $classMetaData = $entityManager->getClassMetadata($association['targetEntity']);

            foreach ($classMetaData->embeddedClasses as $embeddedClassKey => $embeddedClass) {
                $associationsEmbeddables[] = implode('.', array_filter(array($fieldNamePrefix, $association['fieldName'], $embeddedClassKey)));
            }

            $associationMappings = $classMetaData->getAssociationMappings();
            $associationMappings = array_filter($associationMappings, function($associationMapping) {
                return $associationMapping['isOwningSide'] === false;
            });

            if (count($associationMappings) !== 0) {
                $traversedAssociationsEmbeddables = $this->traverseAssociationsForEmbeddables($entityManager, $associationMappings, $association['fieldName']);
                $associationsEmbeddables = array_merge($associationsEmbeddables, $traversedAssociationsEmbeddables);
            }
        }

        return $associationsEmbeddables;
    }

    private function analizeEmbeddables(QueryBuilder $queryBuilder)
    {
        $embeddables = array();
        $entityManager = $queryBuilder->getEntityManager();
        $rootEntities = $queryBuilder->getRootEntities();

        foreach ($rootEntities as $rootEntity) {
            $classMetaData = $entityManager->getClassMetadata($rootEntity);

            foreach ($classMetaData->embeddedClasses as $embeddedClassKey => $embeddedClass) {
                $embeddables[] = $embeddedClassKey;
            }

            $traversedAssociationsEmbeddables = $this->traverseAssociationsForEmbeddables($entityManager, $classMetaData->getAssociationMappings());
            $embeddables = array_merge($embeddables, $traversedAssociationsEmbeddables);
        }

        return $embeddables;
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
            // if the table is an embeddable, the property needs to be removed
            if (array_search(implode('.', $tablesToJoin), $this->embeddables) !== false) {
                array_pop($tablesToJoin);
            }

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
