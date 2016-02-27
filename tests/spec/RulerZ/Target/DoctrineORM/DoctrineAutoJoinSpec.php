<?php

namespace spec\RulerZ\Target\DoctrineORM;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\Expr\Join;
use PhpSpec\ObjectBehavior;

use RulerZ\Target\DoctrineORM\AutoJoin;

class DoctrineAutoJoinSpec extends ObjectBehavior
{
    function it_returns_root_entity_embeddable(EntityManager $em, ClassMetadataInfo $classMetadataInfo)
    {
        $this->beConstructedWith(
            $em,
            ['RootEntity'],
            ['root_alias'],
            []
        );

        $em->getClassMetadata('RootEntity')->willReturn($classMetadataInfo);
        $classMetadataInfo->embeddedClasses = ['embeddable' => 'RootEntityEmbeddable'];
        $classMetadataInfo->getAssociationMappings()->willReturn([]);

        $this->buildAccessPath('embeddable', 'embeddable')->shouldReturn('root_alias.embeddable');
    }

     function it_joins_association_embeddable_needed_tables(QueryBuilder $target, EntityManager $entityManager, ClassMetadataInfo $rootClassMetadataInfo, ClassMetadataInfo $associationClassMetadataInfo)
    {
        $this->beConstructedWith($target, [
            ['association', 'embeddable']
        ]);

        $target->getEntityManager()->willReturn($entityManager);
        $target->getRootEntities()->willReturn(['RootEntity']);

        $entityManager->getClassMetadata('RootEntity')->willReturn($rootClassMetadataInfo);
        $rootClassMetadataInfo->getAssociationMappings()->willReturn([
            [
                'targetEntity' => 'AssociationEntity',
                'fieldName' => 'association'
            ]
        ]);

        $entityManager->getClassMetadata('AssociationEntity')->willReturn($associationClassMetadataInfo);
        $associationClassMetadataInfo->embeddedClasses = ['embeddable' => 'AssociationEntityEmbeddable'];
        $associationClassMetadataInfo->getAssociationMappings()->willReturn([]);

        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getDQLPart('join')->willReturn([]);

        $target->join('root_alias.association', 'rulerz_association')->shouldBeCalled();

        $this->getJoinAlias('embeddable', 'association.embeddable')->shouldReturn('rulerz_association.embeddable');
    }

    function it_uses_association_embeddable_joined_tables(QueryBuilder $target, EntityManager $entityManager, ClassMetadataInfo $rootClassMetadataInfo, ClassMetadataInfo $associationClassMetadataInfo, Join $join)
    {
        $this->beConstructedWith($target, [
            ['association', 'embeddable']
        ]);

        $target->getEntityManager()->willReturn($entityManager);
        $target->getRootEntities()->willReturn(['RootEntity']);

        $entityManager->getClassMetadata('RootEntity')->willReturn($rootClassMetadataInfo);
        $rootClassMetadataInfo->getAssociationMappings()->willReturn([
            [
                'targetEntity' => 'AssociationEntity',
                'fieldName' => 'association'
            ]
        ]);

        $entityManager->getClassMetadata('AssociationEntity')->willReturn($associationClassMetadataInfo);
        $associationClassMetadataInfo->embeddedClasses = ['embeddable' => 'AssociationEntityEmbeddable'];
        $associationClassMetadataInfo->getAssociationMappings()->willReturn([]);

        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getDQLPart('join')->willReturn([
            'root_alias' => [$join]
        ]);

        $join->getJoin()->willReturn('root_alias.association');
        $join->getAlias()->willReturn('association');

        $target->join('root_alias.association', 'rulerz_association')->shouldNotBeCalled();

        $this->getJoinAlias('embeddable', 'association.embeddable')->shouldReturn('association.embeddable');
    }

     function it_joins_association_of_association_embeddable_needed_tables(QueryBuilder $target, EntityManager $entityManager, ClassMetadataInfo $rootClassMetadataInfo, ClassMetadataInfo $parentAssociationClassMetadataInfo, ClassMetadataInfo $associationClassMetadataInfo)
    {
        $this->beConstructedWith($target, [
            ['parent_association', 'association', 'embeddable']
        ]);

        $target->getEntityManager()->willReturn($entityManager);
        $target->getRootEntities()->willReturn(['RootEntity']);

        $entityManager->getClassMetadata('RootEntity')->willReturn($rootClassMetadataInfo);
        $rootClassMetadataInfo->getAssociationMappings()->willReturn([
            [
                'targetEntity' => 'ParentAssociationEntity',
                'fieldName' => 'parent_association'
            ]
        ]);

        $entityManager->getClassMetadata('ParentAssociationEntity')->willReturn($parentAssociationClassMetadataInfo);
        $parentAssociationClassMetadataInfo->getAssociationMappings()->willReturn([
            [
                'targetEntity' => 'AssociationEntity',
                'fieldName' => 'association'
            ]
        ]);

        $entityManager->getClassMetadata('AssociationEntity')->willReturn($associationClassMetadataInfo);
        $associationClassMetadataInfo->embeddedClasses = ['embeddable' => 'AssociationEntityEmbeddable'];
        $associationClassMetadataInfo->getAssociationMappings()->willReturn([]);

        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getDQLPart('join')->willReturn([]);

        $target->join('root_alias.parent_association', 'rulerz_parent_association')->shouldBeCalled();
        $target->join('rulerz_parent_association.association', 'rulerz_association')->shouldBeCalled();

        $this->getJoinAlias('embeddable', 'parent_association.association.embeddable')->shouldReturn('rulerz_association.embeddable');
    }

    function it_joins_needed_tables(QueryBuilder $target)
    {
        $this->beConstructedWith($target, [
            ['group']
        ]);

        $target->getEntityManager()->shouldBeCalled();
        $target->getRootEntities()->willReturn([]);

        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getDQLPart('join')->willReturn([]);

        $target->join('root_alias.group', 'rulerz_group')->shouldBeCalled();

        $this->getJoinAlias('group')->shouldReturn(AutoJoin::ALIAS_PREFIX . 'group');
    }

    function it_uses_joined_tables(QueryBuilder $target, Join $join)
    {
        $this->beConstructedWith($target, [
            ['group']
        ]);

        $target->getEntityManager()->shouldBeCalled();
        $target->getRootEntities()->willReturn([]);

        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getDQLPart('join')->willReturn([
            'root_alias' => [$join]
        ]);

        $join->getJoin()->willReturn('root_alias.group');
        $join->getAlias()->willReturn('aliased_group');

        $target->join('root_alias.group', 'rulerz_aliased_group')->shouldNotBeCalled();

        $this->getJoinAlias('aliased_group')->shouldReturn('aliased_group');
    }
}
