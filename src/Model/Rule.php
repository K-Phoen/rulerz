<?php

namespace RulerZ\Model;

use Hoa\Ruler\Model as HoaModel;
use RulerZ\Visitor\AccessCollectorVisitor;
use RulerZ\Visitor\OperatorCollectorVisitor;

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

    /**
     * Returns a list of used operators.
     *
     * @return \Hoa\Ruler\Model\Operator[]
     */
    public function getOperators()
    {
        $visitor = new OperatorCollectorVisitor();

        return $visitor->visit($this);
    }
}
