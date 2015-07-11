<?php

namespace RulerZ\Visitor;

use Doctrine\ORM\QueryBuilder;
use Hoa\Ruler\Model as AST;

use RulerZ\Model;

class DoctrineQueryBuilderVisitor extends SqlVisitor
{
    /**
     * The QueryBuilder to update.
     *
     * @var QueryBuilder
     */
    private $qb;

    /**
     * Associative list of joined tables and their alias.
     *
     * @var array
     */
    private $joinMap = [];

    /**
     * Constructor.
     *
     * @param QueryBuilder $qb                The query builder being manipulated.
     * @param bool         $allowStarOperator Whether to allow the star operator or not (ie: implicit support of unknown operators).
     */
    public function __construct(QueryBuilder $qb, $allowStarOperator = true)
    {
        parent::__construct($allowStarOperator);

        $this->qb      = $qb;
        $this->joinMap = $this->analizeJoins();
    }

    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $dimensions = $element->getDimensions();

        // simple column access
        if (count($dimensions) === 0) {
            return sprintf('%s.%s', $this->getRootAlias(), $element->getId());
        }

        // this is the real column that we are trying to access
        $finalColumn = array_pop($dimensions);

        // and this is a list of tables that need to be joined
        $tablesToJoin = array_map(function($dimension) {
            return $dimension[1];
        }, $dimensions);
        $tablesToJoin = array_merge([$element->getId()], $tablesToJoin);

        // and here is the auto-join magic
        $joinTo = $this->getRootAlias();
        foreach ($tablesToJoin as $table) {
            $joinAlias = 'j_' . $table;
            $join      = sprintf('%s.%s', $joinTo, $table);

            if (!isset($this->joinMap[$join])) {
                $this->joinMap[$join] = $joinAlias;
                $this->qb->join(sprintf('%s.%s', $joinTo, $table), $joinAlias);
            } else {
                $joinAlias = $this->joinMap[$join];
            }

            $joinTo = $joinAlias;
        }

        return sprintf('%s.%s', $joinTo, $finalColumn[1]);
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // make it a placeholder
        return ':'.$element->getName();
    }

    /**
     * Returns the root alias used by the query builder;
     *
     * @return string
     */
    private function getRootAlias()
    {
        return $this->qb->getRootAliases()[0];
    }

    /**
     * Builds an associative array of already joins tables and their alias.
     *
     * @return array
     */
    private function analizeJoins()
    {
        $joinMap  = [];
        $dqlParts = $this->qb->getDqlParts();

        foreach ($dqlParts['join'][$this->getRootAlias()] as $join) {
            $joinMap[$join->getJoin()] = $join->getAlias();
        }

        return $joinMap;
    }
}
