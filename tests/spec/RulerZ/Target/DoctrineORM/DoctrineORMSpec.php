<?php

declare(strict_types=1);

namespace spec\RulerZ\Target\DoctrineORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

use Doctrine\ORM\Tools\Setup;
use Entity\Doctrine\Player;
use PhpSpec\Exception\Example\SkippingException;
use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Context;
use RulerZ\Model\Executor;
use spec\RulerZ\Target\BaseTargetBehavior;

class DoctrineORMSpec extends BaseTargetBehavior
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct()
    {
        // Dirty but easy way to have real entity metadata
        $paths = [__DIR__.'/../../../../examples/entities'];
        $isDevMode = true;

        // the connection configuration
        $dbParams = [
            'driver' => 'pdo_sqlite',
            'path' => 'sqlite::memory:',
        ];

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

        $this->em = EntityManager::create($dbParams, $config);
    }

    public function it_supports_satisfies_mode(QueryBuilder $qb)
    {
        $this->supports($qb, CompilationTarget::MODE_SATISFIES)->shouldReturn(true);
    }

    public function it_can_filter_query_builders(QueryBuilder $qb)
    {
        $this->supports($qb, CompilationTarget::MODE_FILTER)->shouldReturn(true);
    }

    public function it_can_returns_an_executor_model()
    {
        $context = $this->createContext();
        $rule = '1 = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->shouldHaveType(Executor::class);

        $executorModel->getTraits()->shouldHaveCount(2);
        $executorModel->getCompiledRule()->shouldReturn('"1 = 1"');
    }

    public function it_prefixes_column_accesses_with_the_right_entity_alias()
    {
        $context = $this->createContext();
        $rule = 'points >= 1';
        $expectedRule = '"player.points >= 1"';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->getCompiledRule()->shouldReturn($expectedRule);
    }

    public function it_supports_positional_parameters()
    {
        $context = $this->createContext();
        $rule = 'points >= ?';
        $expectedRule = '"player.points >= ?0"';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->getCompiledRule()->shouldReturn($expectedRule);
    }

    public function it_uses_the_metadata_to_join_tables()
    {
        $context = $this->createContext();
        $rule = 'group.name = "ADMIN"';
        $expectedRule = '"_0_group.name = \'ADMIN\'"';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->getCompiledRule()->shouldReturn($expectedRule);
        $executorModel->getCompiledData()->shouldReturn([
            'detectedJoins' => [
                [
                    'root' => 'player',
                    'column' => 'group',
                    'as' => '_0_group',
                ],
            ],
        ]);
    }

    public function it_does_not_duplicate_join_tables()
    {
        $context = $this->createContext();
        $rule = 'group.name = "ADMIN" or group.name = "OWNER"';
        $expectedRule = '"(_0_group.name = \'ADMIN\' OR _0_group.name = \'OWNER\')"';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->getCompiledRule()->shouldReturn($expectedRule);
        $executorModel->getCompiledData()->shouldReturn([
            'detectedJoins' => [
                [
                    'root' => 'player',
                    'column' => 'group',
                    'as' => '_0_group',
                ],
            ],
        ]);
    }

    public function it_generates_a_different_identifier_for_contexts_with_joins(Expr\Join $join)
    {
        $rule = 'group.name = "ADMIN" or group.name = "OWNER"';
        $joinLessContext = $this->createContext();
        $join->getJoin()->willReturn('test.group');
        $join->getAlias()->willReturn('grp');
        $contextWithJoins = $this->createContext();
        $contextWithJoins['joins'] = ['some_root' => [$join->getWrappedObject()]];

        $joinLessIdentifier = $this->getRuleIdentifierHint($rule, $joinLessContext)->getWrappedObject();
        $this->getRuleIdentifierHint($rule, $contextWithJoins)->shouldNotReturn($joinLessIdentifier);
    }

    public function it_uses_the_metadata_to_detect_invalid_attribute_access()
    {
        $context = $this->createContext();
        $rule = 'attr_does_not_exist = "ADMIN"';

        $this->shouldThrow(new \Exception('"attr_does_not_exist" not found for entity "Entity\Doctrine\Player"'))->duringCompile($this->parseRule($rule), $context);
    }

    public function it_uses_the_metadata_to_detect_invalid_joins()
    {
        $context = $this->createContext();
        $rule = 'does_not_exist.name = "ADMIN"';

        $this->shouldThrow(new \Exception('"does_not_exist" not found for entity "Entity\Doctrine\Player"'))->duringCompile($this->parseRule($rule), $context);
    }

    public function it_uses_the_metadata_to_detect_invalid_attribute_access_on_join()
    {
        $context = $this->createContext();
        $rule = 'group.attr_does_not_exist = "ADMIN"';

        $this->shouldThrow(new \Exception('"attr_does_not_exist" not found for entity "Entity\Doctrine\Group"'))->duringCompile($this->parseRule($rule), $context);
    }

    public function it_implicitly_converts_unknown_operators()
    {
        $context = $this->createContext();
        $rule = 'points >= 42 and always_true(42)';
        $expectedDql = '"(player.points >= 42 AND always_true(42))"';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->getCompiledRule()->shouldReturn($expectedDql);
    }

    public function it_supports_custom_operators()
    {
        throw new SkippingException('Not yet implemented.');
        $rule = 'points > 30 and always_true()';

        $this->defineOperator('always_true', function () {
            throw new \LogicException('should not be called');
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->getCompiledRule()->shouldReturn('"(player.points > 30 AND ".call_user_func($operators["always_true"]).")"');
    }

    public function it_supports_custom_inline_operators()
    {
        $context = $this->createContext();
        $rule = 'points >= 42 and always_true(42)';
        $expectedDql = '"(player.points >= 42 AND inline_always_true(42))"';

        $this->defineInlineOperator('always_true', function ($value) {
            return 'inline_always_true('.$value.')';
        });

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->getCompiledRule()->shouldReturn($expectedDql);
    }

    public function it_reuses_joined_tables()
    {
        $context = new Context([
            'em' => $this->em,
            'root_entities' => [Player::class],
            'root_aliases' => ['player'],
            'joins' => [
                'player' => [
                    new Join(Join::INNER_JOIN, 'player.group', 'joined_group_alias'),
                ],
            ],
        ]);
        $rule = 'joined_group_alias.name = \'FOO\'';
        $expectedDql = '"joined_group_alias.name = \'FOO\'"';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->getCompiledRule()->shouldReturn($expectedDql);
        $executorModel->getCompiledData()->shouldReturn([
            'detectedJoins' => [],
        ]);
    }

    public function it_allows_embedded_classes_to_be_used()
    {
        $context = $this->createContext();
        $rule = 'address.country = \'France\'';
        $expectedDql = '"player.address.country = \'France\'"';

        /** @var Executor $executorModel */
        $executorModel = $this->compile($this->parseRule($rule), $context);
        $executorModel->getCompiledRule()->shouldReturn($expectedDql);
        $executorModel->getCompiledData()->shouldReturn([
            'detectedJoins' => [],
        ]);
    }

    private function createContext(): Context
    {
        return new Context([
            'em' => $this->em,
            'root_entities' => [Player::class],
            'root_aliases' => ['player'],
            'joins' => [],
        ]);
    }
}
