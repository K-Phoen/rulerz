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
     * Constructor.
     *
     * @param QueryBuilder $qb                The query builder being manipulated.a
     * @param bool         $allowStarOperator Whether to allow the star operator or not (ie: implicit support of unknown operators).
     */
    public function __construct(QueryBuilder $qb, $allowStarOperator = true)
    {
        parent::__construct($allowStarOperator);

        $this->qb = $qb;
    }

    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        return sprintf('%s.%s', $this->getRootAlias(), $element->getId());
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
}
