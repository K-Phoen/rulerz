<?php

namespace RulerZ\Exception;

use Hoa\Ruler\Model as AST;

class InvalidAccess extends \RuntimeException
{
    public static function create(AST\Bag\Context $element)
    {
        $dimensionNames = array_map(function ($dimension) {
            return $dimension[1];
        }, $element->getDimensions());
        $dimensionNames = array_merge([$element->getId()], $dimensionNames);

        return new static(sprintf('Invalid access "%s".', implode('.', $dimensionNames)));
    }
}
