<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;
use Hoa\Visitor\Element as VisitorElement;
use Hoa\Visitor\Visit as Visitor;

interface RuleVisitor extends Visitor
{
    /**
     * Visit an access (ie: a column, an attribute or a parameter access)
     *
     * @param AST\Bag\Context $element Element to visit.
     *
     * @return string
     */
    public function visitAccess(AST\Bag\Context $element);

    /**
     * Visit a model
     *
     * @param AST\Model $element Element to visit.
     *
     * @return mixed
     */
    public function visitModel(AST\Model $element);

    /**
     * Visit a scalar
     *
     * @param AST\Bag\Scalar $element Element to visit.
     *
     * @return mixed
     */
    public function visitScalar(AST\Bag\Scalar $element);

    /**
     * Visit an array
     *
     * @param AST\Bag\RulerArray $element Element to visit.
     *
     * @return array
     */
    public function visitArray(AST\Bag\RulerArray $element);

    /**
     * Visit an operator
     *
     * @param AST\Operator $element Element to visit.
     *
     * @return Xcallable
     */
    public function visitOperator(AST\Operator $element);
}
