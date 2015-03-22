<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;
use Hoa\Visitor\Element as VisitorElement;
use Hoa\Visitor\Visit as Visitor;

use RulerZ\Exception\OperatorNotFoundException;

class PommVisitor extends SqlVisitor
{
    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element)
    {
        $name = $element->getId();

        // parameter
        if ($name[0] === ':') {
            return '$*';
        }

        return $element->getId();
    }

    /**
     * {@inheritDoc}
     */
    protected function defineBuiltInOperators()
    {
        parent::defineBuiltInOperators();

        $this->setOperator('xor',  function ($a, $b) { return sprintf('%s # %s', $a, $b); });
    }
}
