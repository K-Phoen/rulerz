<?php

namespace RulerZ\Compiler\Visitor;

use Hoa\Ruler\Model as AST;

use RulerZ\Model;

class NativeVisitor extends GenericVisitor
{
    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $flattenedDimensions = [
            sprintf('["%s"]', $element->getId())
        ];

        foreach ($element->getDimensions() as $dimension) {
            $flattenedDimensions[] = sprintf('["%s"]', $dimension[AST\Bag\Context::ACCESS_VALUE]);
        }

        return '$target' . implode('', $flattenedDimensions);
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        return sprintf('$parameters["%s"]', $element->getName());
    }

    /**
     * {@inheritDoc}
     */
    public function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null)
    {
        return sprintf('array(%s)', implode(', ', parent::visitArray($element, $handle, $eldnah)));
    }

    /**
     * @inheritdoc
     */
    protected function defineBuiltInOperators()
    {
        $this->setInlineOperator('and', function ($a, $b) { return sprintf('(%s && %s)', $a, $b); });
        $this->setInlineOperator('or',  function ($a, $b) { return sprintf('(%s || %s)', $a, $b); });
        $this->setInlineOperator('not', function ($a )    { return sprintf('!(%s)', $a); });
        $this->setInlineOperator('=',   function ($a, $b) { return sprintf('%s == %s', $a, $b); });
        $this->setInlineOperator('is',  function ($a, $b) { return sprintf('%s === %s', $a, $b); });
        $this->setInlineOperator('!=',  function ($a, $b) { return sprintf('%s != %s', $a, $b); });
        $this->setInlineOperator('>',   function ($a, $b) { return sprintf('%s > %s', $a, $b); });
        $this->setInlineOperator('>=',  function ($a, $b) { return sprintf('%s >= %s', $a, $b); });
        $this->setInlineOperator('<',   function ($a, $b) { return sprintf('%s < %s', $a, $b); });
        $this->setInlineOperator('<=',  function ($a, $b) { return sprintf('%s <= %s', $a, $b); });
        $this->setInlineOperator('in',  function ($a, $b) { return sprintf('in_array(%s, %s)', $a, $b); });

        $this->setOperator('sum', function () { return array_sum(func_get_args()); });
    }
}
