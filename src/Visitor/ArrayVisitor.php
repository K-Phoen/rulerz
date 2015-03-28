<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Exception as HoaException;
use Hoa\Ruler\Model as AST;
use Hoa\Ruler\Visitor\Asserter as HoaArrayVisitor;
use Hoa\Visitor\Element as VisitorElement;
use Symfony\Component\PropertyAccess\PropertyAccess;

use RulerZ\Model;

class ArrayVisitor extends HoaArrayVisitor
{
    /**
     * {@inheritDoc}
     */
    public function visit(VisitorElement $element, &$handle = null, $eldnah = null)
    {
        if ($element instanceof Model\Parameter) {
            return $this->visitParameter($element, $handle, $eldnah);
        }

        return parent::visit($element, $handle, $eldnah);
    }

    /**
     * {@inheritDoc}
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

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        $name    = ':'.$element->getName();
        $context = $this->getContext();

        if (!isset($context[$name])) {
            throw new \RuntimeException(sprintf('Parameter "%s" not defined', $name)); // @todo this should be a more specific exception
        }

        return $context[$name];
    }
}
