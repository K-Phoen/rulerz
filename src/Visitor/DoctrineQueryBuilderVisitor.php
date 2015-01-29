<?php

namespace Visitor;

use Doctrine\ORM\QueryBuilder;
use Hoa\Core;
use Hoa\Ruler;
use Hoa\Visitor;

class DoctrineQueryBuilderVisitor implements Visitor\Visit
{
    /**
     * The QueryBuilder to update.
     *
     * @var QueryBuilder
     */
    private $qb;

    /**
     * List of operators.
     *
     * @var array
     */
    private $_operators = [];

    /**
     * Constructor.
     *
     * @param   \Hoa\Ruler\Context  $context    Context.
     */
    public function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;

        $this->setOperator('and', function ($a, $b) { return sprintf('%s AND %s', $a, $b); });
        $this->setOperator('or', function ($a, $b) { return sprintf('%s OR %s', $a, $b); });
        //$this->setOperator('xor', function ($a, $b) { return (bool) ($a ^ $b); });
        //$this->setOperator('not', function ($a)     { return !$a; });
        $this->setOperator('=', function ($a, $b) { return sprintf('%s = %s', $a, $b); });
        $this->setOperator('!=', function ($a, $b) { return sprintf('%s != %s', $a, $b); });
        //$this->setOperator('is',  $this->getOperator('='));
        $this->setOperator('>',   function ($a, $b) { return sprintf('%s > %s', $a,  $b); });
        $this->setOperator('>=',   function ($a, $b) { return sprintf('%s >= %s', $a,  $b); });
        $this->setOperator('<',   function ($a, $b) { return sprintf('%s < %s', $a,  $b); });
        $this->setOperator('<=',   function ($a, $b) { return sprintf('%s <= %s', $a,  $b); });
        $this->setOperator('in',  function ($a, $b) { return sprintf('%s IN %s', $a, $b); });
    }

    /**
     * Visit an element.
     *
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     *
     * @return  string The DQL code for the given rule.
     */
    public function visit(Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        if ($element instanceof Ruler\Model) {
            return $this->visitModel($element, $handle, $eldnah);
        }

        if ($element instanceof Ruler\Model\Operator) {
            return $this->visitOperator($element, $handle, $eldnah);
        }

        if ($element instanceof Ruler\Model\Bag\Scalar) {
            return $this->visitScalar($element, $handle, $eldnah);
        }

        if ($element instanceof Ruler\Model\Bag\RulerArray) {
            return $this->visitArray($element, $handle, $eldnah);
        }

        if ($element instanceof Ruler\Model\Bag\Context) {
            return $this->visitContext($element, $handle, $eldnah);
        }

        throw new \LogicException(sprintf('Element of type "%s" not handled', get_class($element)));
    }

    /**
     * Visit a model
     *
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     *
     * @return  string
     */
    public function visitModel(Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        return $element->getExpression()->accept($this, $handle, $eldnah);
    }

    /**
     * Visit a context (ie: a column access)
     *
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  mixed
     */
    private function visitContext(Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        return sprintf('%s.%s', $this->getRootAlias(), $element->getId());
    }

    /**
     * Visit a scalar
     *
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  mixed
     */
    private function visitScalar(Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $value = $element->getValue();

        return is_numeric($value) ? $value : sprintf("'%s'", $value);
    }

    /**
     * Visit an array
     *
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  array
     */
    private function visitArray(Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $out = array_map(function ($item) use ($handle, $eldnah) {
            return $item->accept($this, $handle, $eldnah);
        }, $element->getArray());

        return sprintf('(%s)', implode(', ', $out));
    }

    /**
     * Visit an operator
     *
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  mixed
     */
    private function visitOperator(Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $operator  = $this->getOperator($element->getName());
        $arguments = array_map(function ($argument) use ($handle, $eldnah) {
            return $argument->accept($this, $handle, $eldnah);
        }, $element->getArguments());

        return $operator->distributeArguments($arguments);
    }

    /**
     * Set an operator.
     *
     * @param   string  $operator     Operator.
     * @param   string  $classname    Classname.
     *
     * @return  DoctrineQueryBuilderVisitor
     */
    public function setOperator($operator, callable $transformer)
    {
        $this->_operators[$operator] = $transformer;

        return $this;
    }

    /**
     * Check if an operator exists.
     *
     * @param   string  $operator    Operator.
     * @return  bool
     */
    public function operatorExists($operator)
    {
        return true === array_key_exists($operator, $this->_operators);
    }

    /**
     * Get an operator.
     *
     * @param   string  $operator    Operator.
     * @return  string
     */
    private function getOperator($operator)
    {
        if (false === $this->operatorExists($operator)) {
            throw new Ruler\Exception\Asserter('Operator "%s" does not exist.', 1, $operator);
        }

        $handle = &$this->_operators[$operator];

        if (!$handle instanceof Core\Consistency\Xcallable) {
            $handle = xcallable($handle);
        }

        return $this->_operators[$operator];
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
