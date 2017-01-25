<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;
use RulerZ\Model;

class ParameterCollectorVisitor extends Visitor
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * {@inheritdoc}
     */
    public function getCompilationData()
    {
        return [
            'parameters' => $this->parameters,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        $this->parameters[$element->getName()] = $element;
    }

    /**
     * {@inheritdoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        parent::visitModel($element, $handle, $eldnah);

        return $this->parameters;
    }
}
