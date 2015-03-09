<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Exception as HoaException;
use Hoa\Ruler\Model as AST;
use Hoa\Ruler\Visitor\Asserter as HoaArrayVisitor;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ArrayVisitor extends HoaArrayVisitor
{
    /**
     * Visit a context
     *
     * @access  protected
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  mixed
     */
    protected function visitContext(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $context  = $this->getContext();

        if ($context === null) {
            throw new HoaException\Asserter('Assert needs a context to work properly.', 0);
        }

        $id = $element->getId();

        if (!isset($context[$id])) {
            throw new HoaException\Asserter('Context reference %s does not exists.', 1, $id);
        }

        $contextPointer = $context[$id];

        foreach ($element->getDimensions() as $dimensionNumber => $dimension) {
            $rawAattribute  = $dimension[AST\Bag\Context::ACCESS_VALUE];
            $attribute      = is_array($contextPointer) ? '['.$rawAattribute.']' : $rawAattribute;

            $contextPointer = $accessor->getValue($contextPointer, $attribute);
        }

        return $contextPointer;
    }
}
