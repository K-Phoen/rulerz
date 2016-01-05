<?php

namespace spec\RulerZ\Executor\DoctrineQueryBuilder;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query\Expr\Join;
use PhpSpec\ObjectBehavior;

use RulerZ\Executor\DoctrineQueryBuilder\AutoJoin;

class AutoJoinSpec extends ObjectBehavior
{
    function it_returns_root_entity_embeddable(QueryBuilder $target, EntityManager $entityManager, ClassMetadataInfo $classMetadataInfo)
    {
        $this->beConstructedWith($target, [
            ['embeddable']
        ]);

        $target->getEntityManager()->willReturn($entityManager);
        $target->getRootEntities()->willReturn(['RootEntity']);

        $entityManager->getClassMetadata('RootEntity')->willReturn($classMetadataInfo);
        $classMetadataInfo->embeddedClasses = ['embeddable' => 'RootEntityEmbeddable'];

        $classMetadataInfo->getAssociationMappings()->willReturn([]);

        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getDQLPart('join')->willReturn([]);

        $this->getJoinAlias('embeddable')->shouldReturn('root_alias.embeddable');  
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
                'fieldName' => 'association',
                'isOwningSide' => false
            ]
        ]);

        $entityManager->getClassMetadata('AssociationEntity')->willReturn($associationClassMetadataInfo);
        $associationClassMetadataInfo->embeddedClasses = ['embeddable' => 'AssociationEntityEmbeddable'];
        $associationClassMetadataInfo->getAssociationMappings()->willReturn([]);

        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getDQLPart('join')->willReturn([]);

        $target->join('root_alias.association', 'rulerz_association')->shouldBeCalled();

        $this->getJoinAlias('association.embeddable')->shouldReturn('rulerz_association.embeddable');
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
                'fieldName' => 'association',
                'isOwningSide' => false
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

        $this->getJoinAlias('association.embeddable')->shouldReturn('association.embeddable');
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
