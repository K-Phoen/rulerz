<?php

namespace RulerZ\Visitor;

use RulerZ\Model;

class EloquentQueryBuilderVisitor extends SqlVisitor
{
    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // make it a placeholder
        return ':'.$element->getName();
    }
}
