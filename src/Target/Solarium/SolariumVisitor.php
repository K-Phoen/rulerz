<?php

namespace RulerZ\Target\Solarium;

use Hoa\Ruler\Model as AST;

use RulerZ\Model;
use RulerZ\Target\GenericVisitor;

class SolariumVisitor extends GenericVisitor
{
    /**
     * {@inheritDoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        $searchQuery = parent::visitModel($element, $handle, $eldnah);

        return "'" . $searchQuery . "'";
    }

    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        return $element->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function visitScalar(AST\Bag\Scalar $element, &$handle = null, $eldnah = null)
    {
        $value = $element->getValue();

        return is_numeric($value) ? $value : sprintf('"%s"', $value);
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // FIXME the parameters handling is REALLY hacky
        $parameterName = $element->getName();

        return "'. \$parameters['$parameterName'] .'";
    }
}
