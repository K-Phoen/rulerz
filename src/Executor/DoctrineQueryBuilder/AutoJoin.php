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
     * List of entities that have been analyzed by the embeddable traverser
     *
     * @var array
     */
    private $analyzedTargetEntities = [];

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

    public function getJoinAlias($table, $fullPropertyPath = null)
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
        if (in_array($fullPropertyPath, $this->embeddables))
        {
            return $this->getEmbeddableAlias($fullPropertyPath);
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

    private function getEmbeddableAlias($fullPropertyPath)
    {
        $embeddableDimensions = explode('.', $fullPropertyPath);

        $embeddableName = array_pop($embeddableDimensions);
        $embeddableTable = array_pop($embeddableDimensions);

        if ($embeddableTable === null)
        {
            // the embeddable is not inside an association, so we use the root alias prefix.
            $alias = $this->queryBuilder->getRootAliases()[0];
        }
        elseif (array_key_exists($embeddableTable, $this->knownAliases))
        {
            // the table name is a known alias (already join for instance) so we
            // don't need to do anything.
            $alias = $embeddableTable;
        }
        elseif (array_key_exists(self::ALIAS_PREFIX . $embeddableTable, $this->knownAliases))
        {
            // otherwise the table should have automatically been joined, so we use our table prefix.
            $alias = self::ALIAS_PREFIX . $embeddableTable;
        }

        if (!isset($alias)) {
            throw new \RuntimeException(sprintf('Could find embeddable "%s"', $fullPropertyPath));
        }

        return $alias . '.' . $embeddableName;
    }

    private function traverseAssociationsForEmbeddables(EntityManager $entityManager, array $associations, $fieldNamePrefix = false)
    {
        $associationsEmbeddables = array();

        foreach ($associations as $association) {
            $classMetaData = $entityManager->getClassMetadata($association['targetEntity']);
            $this->analyzedTargetEntities[] = $association['targetEntity'];

            foreach ($classMetaData->embeddedClasses as $embeddedClassKey => $embeddedClass) {
                $associationsEmbeddables[] = implode('.', array_filter(array($fieldNamePrefix, $association['fieldName'], $embeddedClassKey)));
            }

            $associationMappings = $classMetaData->getAssociationMappings();
            $associationMappings = array_filter($associationMappings, function($associationMapping) {
                return !in_array($associationMapping['targetEntity'], $this->analyzedTargetEntities);
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

            // Since this is a root entity embeddable, there is no need to join.
            foreach ($this->expectedJoinChains as $tablesToJoinKey => $tablesToJoin) {
                if (in_array(implode('.', $tablesToJoin), $embeddables)) {
                    unset($this->expectedJoinChains[$tablesToJoinKey]);
                }
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
                array_shift($tablesToJoin);
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
