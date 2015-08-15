<?php

namespace RulerZ\Compiler\Target;

use Hoa\Ruler\Model as AST;

use RulerZ\Model;

class ArrayVisitor extends GenericVisitor
{
    /**
     * @inheritDoc
     */
    public function supports($target, $mode)
    {
        // we can filter a collection
        if ($mode === self::MODE_FILTER) {
            return is_array($target) || $target instanceof \Traversable;
        }

        // and we know how to handle arrays and objects
        return is_array($target) || is_object($target);
    }

    /**
     * {@inheritDoc}
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\ArrayTarget\FilterTrait',
            '\RulerZ\Executor\ArrayTarget\SatisfiesTrait',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function visitScalar(AST\Bag\Scalar $element, &$handle = null, $eldnah = null)
    {
        return var_export(parent::visitScalar($element, $handle, $eldnah), true);
    }

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
        return var_export(parent::visitArray($element, $handle, $eldnah), true);
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
