<?php

namespace RulerZ\Compiler\Visitor;

use Hoa\Ruler\Model as AST;
use Hoa\Visitor\Element as VisitorElement;

use RulerZ\Compiler\RuleVisitor;
use RulerZ\Compiler\Target\Polyfill;
use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Model;

/**
 * Generic visitor intended to be extended.
 */
abstract class GenericVisitor implements RuleVisitor
{
    use Polyfill\Operators;

    /**
     * Define the built-in operators.
     */
    abstract protected function defineBuiltInOperators();

    /**
     * @inheritdoc
     *
     * @note The aim of this method is to be overriden.
     */
    public function getCompilationData()
    {
        return [];
    }

    /**
     * @param array<callable> $operators List of custom operators to register.
     * @param array<callable> $inlineOperators List of custom inline operators to register.
     */
    public function __construct(array $operators = [], array $inlineOperators = [])
    {
        $this->defineBuiltInOperators();

        $this->setOperators($operators);
        $this->setInlineOperators($inlineOperators);
    }

    /**
     * {@inheritDoc}
     */
    public function visit(VisitorElement $element, &$handle = null, $eldnah = null)
    {
        if ($element instanceof Model\Rule) {
            return $this->visitModel($element, $handle, $eldnah);
        }

        if ($element instanceof AST\Operator) {
            return $this->visitOperator($element, $handle, $eldnah);
        }

        if ($element instanceof AST\Bag\Scalar) {
            return $this->visitScalar($element, $handle, $eldnah);
        }

        if ($element instanceof AST\Bag\RulerArray) {
            return $this->visitArray($element, $handle, $eldnah);
        }

        if ($element instanceof AST\Bag\Context) {
            return $this->visitAccess($element, $handle, $eldnah);
        }

        if ($element instanceof Model\Parameter) {
            return $this->visitParameter($element, $handle, $eldnah);
        }

        throw new \LogicException(sprintf('Element of type "%s" not handled', get_class($element)));
    }

    /**
     * {@inheritDoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        return $element->getExpression()->accept($this, $handle, $eldnah);
    }

    /**
     * {@inheritDoc}
     */
    public function visitScalar(AST\Bag\Scalar $element, &$handle = null, $eldnah = null)
    {
        return var_export($element->getValue(), true);
    }

    /**
     * {@inheritDoc}
     */
    public function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null)
    {
        return array_map(function ($item) use (&$handle, $eldnah) {
            return $item->accept($this, $handle, $eldnah);
        }, $element->getArray());
    }

    /**
     * {@inheritDoc}
     */
    public function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null)
    {
        $operatorName = $element->getName();

        // the operator does not exist at all, throw an error before doing anything else.
        if (!$this->hasInlineOperator($operatorName) && !$this->hasOperator($operatorName)) {
            throw new OperatorNotFoundException($operatorName, sprintf('Operator "%s" does not exist.', $operatorName));
        }

        // expand the arguments
        $arguments = array_map(function ($argument) use (&$handle, $eldnah) {
            return $argument->accept($this, $handle, $eldnah);
        }, $element->getArguments());

        // and either inline the operator call
        if ($this->hasInlineOperator($operatorName)) {
            $callable = $this->getInlineOperator($operatorName);

            return call_user_func_array($callable, $arguments);
        }

        $inlinedArguments = empty($arguments) ? '' : ', ' . implode(', ', $arguments);

        // or defer it.
        return sprintf('call_user_func($operators["%s"]%s)', $operatorName, $inlinedArguments);
    }
}
