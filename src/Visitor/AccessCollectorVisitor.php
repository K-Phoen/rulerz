<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;

class AccessCollectorVisitor extends Visitor
{
    /**
     * @var array
     */
    private $accesses = [];

    /**
     * {@inheritDoc}
     */
    public function getCompilationData()
    {
        return [
            'accesses' => $this->accesses,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $this->accesses[] = $element;
    }

    /**
     * {@inheritDoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        parent::visitModel($element, $handle, $eldnah);

        return $this->accesses;
    }
}
