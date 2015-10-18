<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;

class OperatorCollectorVisitor extends Visitor
{
    /**
     * @var array
     */
    private $operators = [];

    /**
     * {@inheritDoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        parent::visitModel($element, $handle, $eldnah);

        return $this->operators;
    }

    /**
     * {@inheritDoc}
     */
    public function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null)
    {
        parent::visitOperator($element, $handle, $eldnah);

        $this->operators[] = $element;
    }
}
