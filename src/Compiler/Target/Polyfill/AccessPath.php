<?php

namespace RulerZ\Compiler\Target\Polyfill;

use Hoa\Ruler\Model as AST;

trait AccessPath
{
    /**
     * @param AST\Bag\Context $element Element to visit.
     *
     * @return string
     */
    private function flattenAccessPath(AST\Bag\Context $element)
    {
        $flattenedDimensions = [$element->getId()];
        foreach ($element->getDimensions() as $dimension) {
            $flattenedDimensions[] = $dimension[1];
        }

        return implode('.', $flattenedDimensions);
    }
}
