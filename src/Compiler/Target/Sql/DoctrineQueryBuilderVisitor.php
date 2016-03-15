<?php

namespace RulerZ\Compiler\Target\Sql;

use Doctrine\ORM\QueryBuilder;
use Hoa\Ruler\Model as AST;
use RulerZ\Model;

class DoctrineQueryBuilderVisitor extends GenericSqlVisitor
{
    /**
     * The root alias used by the query builder.
     */
    const ROOT_ALIAS_PLACEHOLDER = '@@_ROOT_ALIAS_@@';

    /**
     * @var array
     */
    private $detectedJoins = [];

    /**
     * {@inheritdoc}
     */
    public function compile(Model\Rule $rule)
    {
        $executor = parent::compile($rule);
        $this->reset();

        return $executor;
    }

    /**
     * @inheritDoc
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder;
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\DoctrineQueryBuilder\FilterTrait',
            '\RulerZ\Executor\DoctrineQueryBuilder\AutoJoinTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getCompilationData()
    {
        return [
            'detectedJoins' => $this->detectedJoins,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        $dql = parent::visitModel($element, $handle, $eldnah);

        return '"' . $dql . '"';
    }

    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $dimensions = $element->getDimensions();

        // simple column access
        if (count($dimensions) === 0) {
            return sprintf('%s.%s', self::ROOT_ALIAS_PLACEHOLDER, parent::visitAccess($element, $handle, $eldnah));
        }

        // this is the real column that we are trying to access
        $finalColumn = array_pop($dimensions)[1];

        // and this is a list of tables that need to be joined
        $tablesToJoin = array_map(function ($dimension) {
            return $dimension[1];
        }, $dimensions);
        $tablesToJoin = array_merge([$element->getId()], $tablesToJoin);

        $this->detectedJoins[] = $tablesToJoin;

        return sprintf('" . $this->getJoinAlias($target, "%s", "%s") . ".%s', end($tablesToJoin), implode('.', $tablesToJoin), $finalColumn);
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // make it a placeholder
        return ':' . $element->getName();
    }

    protected function reset()
    {
        $this->detectedJoins = [];
    }
}
