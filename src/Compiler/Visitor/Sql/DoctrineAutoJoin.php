<?php

namespace RulerZ\Compiler\Visitor\Sql;

use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Hoa\Ruler\Model as AST;
use RulerZ\Exception;
use RulerZ\Model;

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
     * @var array<Entity,alias>
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
        $this->rootEntities = $rootEntities;

        $this->knownEntities = array_combine($rootEntities, $rootAliases);
        $this->aliasMap = array_flip($this->knownEntities);

        foreach ($existingJoins as $fromAlias => $joins) {
            /** @var \Doctrine\ORM\Query\Expr\Join $join */
            foreach ($joins as $join) {
                list(, $attribute) = explode('.', $join->getJoin());
                $relation = $this->getRelation($attribute, $this->aliasMap[$fromAlias]);

                $this->knownEntities[$relation['targetEntity']] = $join->getAlias();
                $this->aliasMap[$join->getAlias()] = $relation['targetEntity'];
            }
        }
    }

    public function getDetectedJoins()
    {
        return $this->detectedJoins;
    }

    public function buildAccessPath(AST\Bag\Context $element)
    {
        $dimensionNames = array_map(function ($dimension) {
            return $dimension[1];
        }, $element->getDimensions());
        array_unshift($dimensionNames, $element->getId());

        $currentEntity = current($this->rootEntities);

        foreach ($dimensionNames as $i => $dimension) {
            if (!$this->em->getClassMetadata($currentEntity)) {
                throw new \Exception(sprintf('Metadata not found for entity "%s"', $currentEntity));
            }

            // the current dimension is an alias (a table already joined for instance)
            if (isset($this->aliasMap[$dimension])) {
                $currentEntity = $this->aliasMap[$dimension];
                continue;
            }

            // the current dimension is a relation of the current entity
            if ($this->relationExists($dimension, $currentEntity)) {
                /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
                $classMetadata = $this->em->getClassMetadata($currentEntity);
                $association = $classMetadata->getAssociationMapping($dimension);

                if (!isset($this->knownEntities[$association['targetEntity']])) {
                    $alias = sprintf('_%d_%s', count($this->knownEntities), $dimension);

                    $this->knownEntities[$association['targetEntity']] = $alias;
                    $this->aliasMap[$alias] = $association['targetEntity'];
                }

                $this->detectedJoins[] = [
                    'root' => $this->knownEntities[$currentEntity],
                    'column' => $dimension,
                    'as' => $this->knownEntities[$association['targetEntity']],
                ];

                $currentEntity = $association['targetEntity'];
                continue;
            }

            // the current dimension is an embedded class
            if ($this->embeddedExists($dimension, $currentEntity)) {
                /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
                $classMetadata = $this->em->getClassMetadata($currentEntity);
                $embeddableMetadata = $classMetadata->embeddedClasses[$dimension];

                $this->knownEntities[$embeddableMetadata['class']] = $this->knownEntities[$currentEntity] . '.' .$dimension;
                $currentEntity = $embeddableMetadata['class'];

                continue;
            }

            // or, at last, it's a column access.
            if ($this->columnExists($dimension, $currentEntity)) {
                if (($i + 1) === count($dimensionNames)) {
                    return sprintf('%s.%s', $this->knownEntities[$currentEntity], $dimension);
                }

                throw new \RuntimeException('Found scalar attribute in the middle of an access path.');
            }

            throw new \Exception(sprintf('"%s" not found for entity "%s"', $dimension, $currentEntity));
        }
    }

    private function columnExists($name, $rootEntity)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $this->em->getClassMetadata($rootEntity);

        return $classMetadata->hasField($name);
    }

    private function relationExists($name, $rootEntity)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $this->em->getClassMetadata($rootEntity);

        return $classMetadata->hasAssociation($name);
    }

    private function getRelation($name, $rootEntity)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $this->em->getClassMetadata($rootEntity);

        return $classMetadata->getAssociationMapping($name);
    }

    private function embeddedExists($name, $rootEntity)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $this->em->getClassMetadata($rootEntity);

        return isset($classMetadata->embeddedClasses) && isset($classMetadata->embeddedClasses[$name]);
    }
}
