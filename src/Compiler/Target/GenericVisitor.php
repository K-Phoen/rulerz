<?php

namespace RulerZ\Compiler\Target;

use Hoa\Ruler\Model as AST;
use Hoa\Visitor\Element as VisitorElement;

use RulerZ\Compiler\RuleVisitor;
use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Model;

/**
 * Generic visitor intended to be extended.
 */
abstract class GenericVisitor implements CompilationTarget, RuleVisitor
{
    /**
     * List of operators.
     *
     * @var array
     */
    private $operators = [];

    /**
     * List of inline-able operators.
     *
     * @var array
     */
    private $inlineOperators = [];

    /**
     * Gets a list of the traits to use in the executor's code.
     *
     * @return array
     */
    abstract protected function getExecutorTraits();

    /**
     * Define the built-in operators.
     */
    abstract protected function defineBuiltInOperators();

    /**
     * Constructor.
     *
     * @param array<callable> $operators The custom operators to register.
     */
    public function __construct(array $operators = [])
    {
        $this->defineBuiltInOperators();

        $this->setOperators($operators);
    }

    /**
     * @inheritDoc
     */
    public function compile(Model\Rule $rule)
    {
        $compiledCode = $this->visit($rule);

        return new Model\Executor(
            $this->getExecutorTraits(),
            $compiledCode
        );
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
        return $element->getValue();
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

    /**
     * Tells if an operator exists.
     *
     * @param string $operator Operator.
     *
     * @return bool
     */
    public function hasOperator($operator)
    {
        return isset($this->operators[$operator]);
    }

    /**
     * Add operators.
     *
     * @param array $operators A list of operators to add.
     *
     * @return self
     */
    public function setOperators(array $operators)
    {
        foreach ($operators as $name => $callable) {
            $this->setOperator($name, $callable);
        }

        return $this;
    }

    /**
     * Set an operator.
     *
     * @param string   $operator    Operator.
     * @param callable $transformer Callable.
     *
     * @return self
     */
    public function setOperator($operator, callable $transformer)
    {
        unset($this->inlineOperators[$operator]);
        $this->operators[$operator] = $transformer;

        return $this;
    }

    /**
     * Get an operator.
     *
     * @param string $operator Operator.
     *
     * @return callable
     */
    protected function getOperator($operator)
    {
        if (!$this->hasOperator($operator)) {
            throw new OperatorNotFoundException($operator, sprintf('Operator "%s" does not exist.', $operator));
        }

        return $this->operators[$operator];
    }

    /**
     * Get the operators list.
     *
     * @return array<callable>
     */
    public function getOperators()
    {
        return $this->operators;
    }

    /**
     * Gets an inline-able operator.
     *
     * @param string $operator Operator.
     *
     * @return callable
     */
    public function getInlineOperator($operator)
    {
        if (!$this->hasInlineOperator($operator)) {
            throw new OperatorNotFoundException($operator, sprintf('Inline operator "%s" does not exist.', $operator));
        }

        return $this->inlineOperators[$operator];
    }

    /**
     * Tells if an inline-able operator exists.
     *
     * @param string $operator Operator.
     *
     * @return bool
     */
    public function hasInlineOperator($operator)
    {
        return isset($this->inlineOperators[$operator]);
    }

    /**
     * Set an inline-able operator.
     *
     * @param string   $operator    Operator.
     * @param callable $transformer Callable.
     */
    public function setInlineOperator($operator, callable $transformer)
    {
        unset($this->operators[$operator]);
        $this->inlineOperators[$operator] = $transformer;
    }
}
