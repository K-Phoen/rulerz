<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;
use Symfony\Component\PropertyAccess\PropertyAccess;

use RulerZ\Model;

class ArrayVisitor extends GenericVisitor
{
    use Polyfill\Parameters;

    /**
     * The context used for the evaluation.
     */
    private $context;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->defineBuiltInOperators();
    }

    /**
     * Define the context to be used.
     *
     * @param mixed $context The context.
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $id       = $element->getId();

        if (!isset($this->context[$id])) {
            throw new \RuntimeException('Context reference %s does not exists.', 1, $id);
        }

        $contextPointer = $this->context[$id];

        foreach ($element->getDimensions() as $dimensionNumber => $dimension) {
            $rawAattribute  = $dimension[AST\Bag\Context::ACCESS_VALUE];
            $attribute      = is_array($contextPointer) ? '['.$rawAattribute.']' : $rawAattribute;

            $contextPointer = $accessor->getValue($contextPointer, $attribute);
        }

        return $contextPointer;
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        return $this->lookupParameter($element->getName());
    }

    /**
     * Define the built-in operators.
     */
    private function defineBuiltInOperators()
    {
        $this->setOperator('and', function ($a = false, $b = false) { return $a && $b; });
        $this->setOperator('or',  function ($a = false, $b = false) { return $a || $b; });
        $this->setOperator('xor', function ($a, $b) { return (bool) ($a ^ $b); });
        $this->setOperator('not', function ($a )     { return !$a; });
        $this->setOperator('=',   function ($a, $b) { return $a === $b; });
        $this->setOperator('is',  $this->getOperator('='));
        $this->setOperator('!=',  function ($a, $b) { return $a != $b; });
        $this->setOperator('>',   function ($a, $b) { return $a >  $b; });
        $this->setOperator('>=',  function ($a, $b) { return $a >= $b; });
        $this->setOperator('<',   function ($a, $b) { return $a <  $b; });
        $this->setOperator('<=',  function ($a, $b) { return $a <= $b; });
        $this->setOperator('in',  function ($a, array $b ) { return in_array($a, $b); });
        $this->setOperator('sum', function () { return array_sum(func_get_args()); });
    }
}
