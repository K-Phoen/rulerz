<?php

namespace RulerZ\Visitor;

use Doctrine\ORM\QueryBuilder;
use Hoa\Ruler\Model as AST;

use RulerZ\Exception\OperatorNotFoundException;

class DoctrineQueryBuilderVisitor extends GenericVisitor
{
    /**
     * The QueryBuilder to update.
     *
     * @var QueryBuilder
     */
    public $qb;

    /**
     * Allow star operator.
     *
     * @var bool
     */
    public $allowStarOperator = true;

    /**
     * Constructor.
     *
     * @param QueryBuilder $qb                The query builder being manipulated.a
     * @param bool         $allowStarOperator Whether to allow the star operator or not (ie: implicit support of unknown operators).
     */
    public function __construct(QueryBuilder $qb, $allowStarOperator = true)
    {
        $this->qb = $qb;
        $this->allowStarOperator = (bool) $allowStarOperator;

        $this->setOperator('and',  function ($a, $b) { return sprintf('%s AND %s', $a, $b); });
        $this->setOperator('or',   function ($a, $b) { return sprintf('%s OR %s', $a, $b); });
        //$this->setOperator('xor', function ($a, $b) { return (bool) ($a ^ $b); });
        $this->setOperator('not',  function ($a) {     return sprintf('NOT (%s)', $a); });
        $this->setOperator('=',    function ($a, $b) { return sprintf('%s = %s', $a, $b); });
        $this->setOperator('!=',   function ($a, $b) { return sprintf('%s != %s', $a, $b); });
        //$this->setOperator('is',  $this->getOperator('='));
        $this->setOperator('>',    function ($a, $b) { return sprintf('%s > %s', $a,  $b); });
        $this->setOperator('>=',   function ($a, $b) { return sprintf('%s >= %s', $a,  $b); });
        $this->setOperator('<',    function ($a, $b) { return sprintf('%s < %s', $a,  $b); });
        $this->setOperator('<=',   function ($a, $b) { return sprintf('%s <= %s', $a,  $b); });
        $this->setOperator('in',   function ($a, $b) { return sprintf('%s IN %s', $a, $b[0] === '(' ? $b : '('.$b.')'); });
        $this->setOperator('like', function ($a, $b) { return sprintf('%s LIKE %s', $a, $b); });
    }

    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element)
    {
        $name = $element->getId();

        // parameter
        if ($name[0] === ':') {
            return $name;
        }

        return sprintf('%s.%s', $this->getRootAlias(), $element->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function visitScalar(AST\Bag\Scalar $element)
    {
        $value = $element->getValue();

        return is_numeric($value) ? $value : sprintf("'%s'", $value);
    }

    /**
     * {@inheritDoc}
     */
    public function visitArray(AST\Bag\RulerArray $element)
    {
        $out = array_map(function ($item) {
            return $item->accept($this);
        }, $element->getArray());

        return sprintf('(%s)', implode(', ', $out));
    }

    /**
     * {@inheritDoc}
     */
    public function visitOperator(AST\Operator $element)
    {
        try {
            $xcallable = $this->getOperator($element->getName());
        } catch (OperatorNotFoundException $e) {
            if (!$this->allowStarOperator) {
                throw $e;
            }

            $xcallable = $this->getStarOperator($element);
        }

        $arguments = array_map(function ($argument) {
            return $argument->accept($this);
        }, $element->getArguments());

        return $xcallable->distributeArguments($arguments);
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
     * Return a "*" or "catch all" operator.
     *
     * @param Visitor\Element $element The node representing the operator.
     *
     * @return \Hoa\Core\Consistency\Xcallable
     */
    private function getStarOperator(AST\Operator $element)
    {
        return xcallable(function () use ($element) {
            return sprintf('%s(%s)', $element->getName(), implode(', ', func_get_args()));
        });
    }
}
