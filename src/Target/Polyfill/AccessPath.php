<?php

declare(strict_types=1);

namespace RulerZ\Target\Polyfill;

use Hoa\Ruler\Model as AST;

trait AccessPath
{
    /**
     * @param AST\Bag\Context $element Element to visit.
     */
    private function flattenAccessPath(AST\Bag\Context $element): string
    {
        $flattenedDimensions = [$element->getId()];
        foreach ($element->getDimensions() as $dimension) {
            $flattenedDimensions[] = $dimension[1];
        }

        return implode('.', $flattenedDimensions);
    }
}
