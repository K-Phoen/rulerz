<?php

declare(strict_types=1);

namespace RulerZ\Target\DoctrineORM;

use Doctrine\ORM\EntityManagerInterface as EntityManager;

class DoctrineAutoJoin
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $detectedJoins = [];

    /**
     * @var array<Entity,array<dimmension,alias>
     */
    private $knownEntities = [];

    /**
     * @var array<alias,Entity>
     */
    private $aliasMap = [];

    /**
     * @var array
     */
    private $rootEntities = [];

    public function __construct(EntityManager $em, array $rootEntities, array $rootAliases, array $existingJoins)
    {
        $this->em = $em;
        $this->rootEntities = array_combine($rootAliases, $rootEntities);

        $this->aliasMap = array_combine($rootAliases, $rootEntities);

        foreach ($existingJoins as $joins) {
            /** @var \Doctrine\ORM\Query\Expr\Join $join */
            foreach ($joins as $join) {
                list($fromAlias, $attribute) = explode('.', $join->getJoin());
                $relation = $this->getRelation($attribute, $this->getEntity($fromAlias));

                $this->saveAlias($relation['targetEntity'], $relation['fieldName'], $join->getAlias(), $fromAlias);
            }
        }
    }

    public function getDetectedJoins(): array
    {
        return $this->detectedJoins;
    }

    public function buildAccessPath(array $dimensionNames): string
    {
        $currentEntity = current($this->rootEntities);
        $lastAlias = key($this->aliasMap);

        foreach ($dimensionNames as $i => $dimension) {
            if (!$this->em->getClassMetadata($currentEntity)) {
                throw new \Exception(sprintf('Metadata not found for entity "%s"', $currentEntity));
            }

            // the current dimension is an alias (a table already joined for instance)
            if (isset($this->aliasMap[$dimension])) {
                $currentEntity = $this->getEntity($dimension);
                $lastAlias = $dimension;

                continue;
            }

            // the current dimension is a relation of the current entity
            if ($this->relationExists($dimension, $currentEntity)) {
                /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
                $classMetadata = $this->em->getClassMetadata($currentEntity);
                $association = $classMetadata->getAssociationMapping($dimension);
                $targetEntity = $association['targetEntity'];

                if (!isset($this->knownEntities[$targetEntity], $this->knownEntities[$targetEntity][$lastAlias][$association['fieldName']])) {
                    $alias = sprintf('_%d_%s', count($this->knownEntities, COUNT_RECURSIVE), $dimension);

                    $this->saveAlias($targetEntity, $association['fieldName'], $alias, $lastAlias);

                    $this->detectedJoins[] = [
                        'root' => $lastAlias,
                        'column' => $dimension,
                        'as' => $alias = $this->getAlias($targetEntity, $association['fieldName'], $lastAlias),
                    ];
                } else {
                    $alias = $this->getAlias($targetEntity, $association['fieldName'], $lastAlias);
                }

                $currentEntity = $targetEntity;
                $lastAlias = $alias;

                continue;
            }

            // the current dimension is an embedded class
            if ($this->embeddedExists($dimension, $currentEntity)) {
                /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
                $classMetadata = $this->em->getClassMetadata($currentEntity);
                $embeddableMetadata = $classMetadata->embeddedClasses[$dimension];

                $currentEntity = $embeddableMetadata['class'];
                $lastAlias = $lastAlias.'.'.$dimension;

                continue;
            }

            // or, at last, it's a column access.
            if ($this->columnExists($dimension, $currentEntity)) {
                if (($i + 1) === count($dimensionNames)) {
                    return sprintf('%s.%s', $lastAlias, $dimension);
                }

                throw new \RuntimeException('Found scalar attribute in the middle of an access path.');
            }

            throw new \Exception(sprintf('"%s" not found for entity "%s"', $dimension, $currentEntity));
        }

        return $lastAlias;
    }

    private function columnExists(string $name, string $rootEntity): bool
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $this->em->getClassMetadata($rootEntity);

        return $classMetadata->hasField($name);
    }

    private function relationExists(string $name, string $rootEntity): bool
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $this->em->getClassMetadata($rootEntity);

        return $classMetadata->hasAssociation($name);
    }

    private function getRelation(string $name, string $rootEntity): array
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $this->em->getClassMetadata($rootEntity);

        return $classMetadata->getAssociationMapping($name);
    }

    private function embeddedExists(string $name, string $rootEntity): bool
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $this->em->getClassMetadata($rootEntity);

        return isset($classMetadata->embeddedClasses) && isset($classMetadata->embeddedClasses[$name]);
    }

    private function saveAlias(string $entity, string $dimension, string $alias, string $parentAlias): void
    {
        if (!isset($this->knownEntities[$entity])) {
            $this->knownEntities[$entity] = [];
        }

        if (!isset($this->knownEntities[$entity][$parentAlias])) {
            $this->knownEntities[$entity][$parentAlias] = [];
        }

        $this->knownEntities[$entity][$parentAlias][$dimension] = $alias;
        $this->aliasMap[$alias] = $entity;
    }

    private function getAlias(string $entity, string $dimension, string $parentAlias): string
    {
        return $this->knownEntities[$entity][$parentAlias][$dimension];
    }

    private function getEntity(string $entity): string
    {
        return $this->aliasMap[$entity];
    }
}
