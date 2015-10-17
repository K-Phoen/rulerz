<?php

namespace RulerZ\Model;

use Hoa\Ruler\Model as HoaModel;
use RulerZ\Visitor\AccessCollectorVisitor;

class Rule extends HoaModel\Model
{
    /**
     * Returns a list of accessed variables.
     *
     * @return \Hoa\Ruler\Model\Bag\Context[]
     */
    public function getAccesses()
    {
        $visitor = new AccessCollectorVisitor();

        return $visitor->visit($this);
    }
}
