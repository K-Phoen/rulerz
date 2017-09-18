<?php

namespace RulerZ\Target;

use Hoa\Ruler\Model as AST;
use Hoa\Visitor\Element as VisitorElement;

use RulerZ\Compiler\RuleVisitor;
use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Model;
use RulerZ\Target\Operators\Definitions as OperatorsDefinitions;

/**
 * Generic visitor intended to be extended.
 */
abstract class GenericVisitor implements RuleVisitor
{
    /**
     * @var OperatorsDefinitions
     */
    protected $operators;

    /**
     * {@inheritdoc}
     *
     * @note The aim of this method is to be overriden.
     */
    public function getCompilationData()
    {
        return [];
    }

    public function __construct(OperatorsDefinitions $operators)
    {
        $this->operators = $operators;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        return $element->getExpression()->accept($this, $handle, $eldnah);
    }

    /**
     * {@inheritdoc}
     */
    public function visitScalar(AST\Bag\Scalar $element, &$handle = null, $eldnah = null)
    {
        return var_export($element->getValue(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null)
    {
        return array_map(function ($item) use (&$handle, $eldnah) {
            return $item->accept($this, $handle, $eldnah);
        }, $element->getArray());
    }

    /**
     * {@inheritdoc}
     */
    public function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null)
    {
        $operatorName = $element->getName();

        // the operator does not exist at all, throw an error before doing anything else.
        if (!$this->operators->hasInlineOperator($operatorName) && !$this->operators->hasOperator($operatorName)) {
            throw new OperatorNotFoundException($operatorName, sprintf('Operator "%s" does not exist.', $operatorName));
        }

        if ($this->operators->hasInlineOperator($operatorName)) {
            return $this->compileInlineOperator($operatorName, $element->getArguments(), $handle, $eldnah);
        }

        return $this->compileRuntimeOperator($operatorName, $element->getArguments(), $handle, $eldnah);
    }

    private function compileRuntimeOperator($operatorName, array $arguments, &$handle = null, $eldnah = null)
    {
        $compiledArguments = array_map(function ($argument) use (&$handle, $eldnah) {
            return sprintf('$this->unwrapArgument(%s)', $argument->accept($this, $handle, $eldnah));
        }, $arguments);
        $inlinedArguments = empty($arguments) ? '' : ', '.implode(', ', $compiledArguments);

        return sprintf('call_user_func($operators["%s"]%s)', $operatorName, $inlinedArguments);
    }

    private function compileInlineOperator($operatorName, array $arguments, &$handle = null, $eldnah = null)
    {
        $operatorCallable = $this->operators->getInlineOperator($operatorName);
        $compiledArguments = array_map(function ($argument) use (&$handle, $eldnah) {
            return $argument->accept($this, $handle, $eldnah);
        }, $arguments);

        return call_user_func_array($operatorCallable, $compiledArguments);
    }
}
