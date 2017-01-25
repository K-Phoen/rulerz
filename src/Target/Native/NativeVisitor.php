<?php

namespace RulerZ\Target\Native;

use Hoa\Ruler\Model as AST;

use RulerZ\Model;
use RulerZ\Target\GenericVisitor;

class NativeVisitor extends GenericVisitor
{
    /**
     * {@inheritdoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $flattenedDimensions = [
            sprintf('["%s"]', $element->getId()),
        ];

        foreach ($element->getDimensions() as $dimension) {
            $flattenedDimensions[] = sprintf('["%s"]', $dimension[AST\Bag\Context::ACCESS_VALUE]);
        }

        return '$target'.implode('', $flattenedDimensions);
    }

    /**
     * {@inheritdoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        return sprintf('$parameters["%s"]', $element->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null)
    {
        return sprintf('array(%s)', implode(', ', parent::visitArray($element, $handle, $eldnah)));
    }
}
