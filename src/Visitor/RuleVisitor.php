<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;
use Hoa\Visitor\Element as VisitorElement;
use Hoa\Visitor\Visit as Visitor;

use RulerZ\Model;

interface RuleVisitor extends Visitor
{
    /**
     * Visit an access (ie: a column, an attribute or a parameter access)
     *
     * @param AST\Bag\Context $element Element to visit.
     * @param mixed           &$handle Handle (reference).
     * @param mixed           $eldnah  Handle (not reference).
     *
     * @return string
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null);

    /**
     * Visit a model
     *
     * @param AST\Model $element Element to visit.
     * @param mixed     &$handle Handle (reference).
     * @param mixed     $eldnah  Handle (not reference).
     *
     * @return mixed
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null);

    /**
     * Visit a scalar
     *
     * @param AST\Bag\Scalar $element Element to visit.
     * @param mixed          &$handle Handle (reference).
     * @param mixed          $eldnah  Handle (not reference).
     *
     * @return mixed
     */
    public function visitScalar(AST\Bag\Scalar $element, &$handle = null, $eldnah = null);

    /**
     * Visit an array
     *
     * @param AST\Bag\RulerArray $element Element to visit.
     * @param mixed              &$handle Handle (reference).
     * @param mixed              $eldnah  Handle (not reference).
     *
     * @return array
     */
    public function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null);

    /**
     * Visit an operator
     *
     * @param AST\Operator $element Element to visit.
     * @param mixed        &$handle Handle (reference).
     * @param mixed        $eldnah  Handle (not reference).
     *
     * @return Xcallable
     */
    public function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null);

    /**
     * Visit a parameter
     *
     * @param Model\Parameter $element Element to visit.
     * @param mixed           &$handle Handle (reference).
     * @param mixed           $eldnah  Handle (not reference).
     *
     * @return mixed
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null);
}
