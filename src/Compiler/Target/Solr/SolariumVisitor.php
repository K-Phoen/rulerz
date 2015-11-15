<?php

namespace RulerZ\Compiler\Target\Solr;

use Hoa\Ruler\Model as AST;
use Solarium\Client as SolariumClient;

use RulerZ\Compiler\Target\GenericVisitor;
use RulerZ\Model;

class SolariumVisitor extends GenericVisitor
{
    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof SolariumClient;
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Solr\SolariumFilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        return var_export(parent::visitModel($element, $handle, $eldnah), true);
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
        // make it a placeholder
        return '$*'; // TODO
    }

    /**
     * Define the built-in operators.
     */
    protected function defineBuiltInOperators()
    {
        $this->setInlineOperator('and',  function ($a, $b) { return sprintf('(%s AND %s)', $a, $b); });
        $this->setInlineOperator('or',   function ($a, $b) { return sprintf('(%s OR %s)', $a, $b); });
        $this->setInlineOperator('not',  function ($a)     { return sprintf('-(%s)', $a); });
        $this->setInlineOperator('=',    function ($a, $b) { return sprintf('%s:%s', $a, $b); });
        $this->setInlineOperator('!=',   function ($a, $b) { return sprintf('%s != %s', $a, $b); });
        $this->setInlineOperator('>',    function ($a, $b) { return sprintf('%s > %s', $a,  $b); });
        $this->setInlineOperator('>=',   function ($a, $b) { return sprintf('%s >= %s', $a,  $b); });
        $this->setInlineOperator('<',    function ($a, $b) { return sprintf('%s < %s', $a,  $b); });
        $this->setInlineOperator('<=',   function ($a, $b) { return sprintf('%s <= %s', $a,  $b); });
        $this->setInlineOperator('in',   function ($a, $b) { return sprintf('%s IN %s', $a, $b[0] === '(' ? $b : '('.$b.')'); });
        $this->setInlineOperator('like', function ($a, $b) { return sprintf('%s LIKE %s', $a, $b); });
    }
}
