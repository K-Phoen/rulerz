<?php

namespace RulerZ\Visitor;

use Hoa\Core\Consistency\Xcallable;
use Hoa\Ruler\Model as AST;
use Hoa\Visitor\Element as VisitorElement;
use Hoa\Visitor\Visit as Visitor;

use RulerZ\Exception\OperatorNotFoundException;

abstract class GenericVisitor implements RuleVisitor
{
    /**
     * List of operators.
     *
     * @var array
     */
    private $operators = [];

    /**
     * Visit an element.
     *
     * @param \VisitorElement $element Element to visit.
     * @param mixed           &$handle Handle (reference).
     * @param mixed           $eldnah  Handle (not reference).
     *
     * @return string The interpreted equivalent of the given rule.
     */
    public function visit(VisitorElement $element, &$handle = null, $eldnah = null)
    {
        if ($element instanceof AST\Model) {
            return $this->visitModel($element);
        }

        if ($element instanceof AST\Operator) {
            return $this->visitOperator($element);
        }

        if ($element instanceof AST\Bag\Scalar) {
            return $this->visitScalar($element);
        }

        if ($element instanceof AST\Bag\RulerArray) {
            return $this->visitArray($element);
        }

        if ($element instanceof AST\Bag\Context) {
            return $this->visitAccess($element);
        }

        throw new \LogicException(sprintf('Element of type "%s" not handled', get_class($element)));
    }

    /**
     * Visit a model
     *
     * @param AST\Model $element Element to visit.
     *
     * @return mixed
     */
    public function visitModel(AST\Model $element)
    {
        return $element->getExpression()->accept($this);
    }

    /**
     * Visit a scalar
     *
     * @param AST\Bag\Scalar $element Element to visit.
     *
     * @return mixed
     */
    public function visitScalar(AST\Bag\Scalar $element)
    {
        return $element->getValue();
    }

    /**
     * Visit an array
     *
     * @param AST\Bag\RulerArray $element Element to visit.
     *
     * @return array
     */
    public function visitArray(AST\Bag\RulerArray $element)
    {
        return array_map(function ($item) {
            return $item->accept($this);
        }, $element->getArray());
    }

    /**
     * Visit an operator
     *
     * @param AST\Operator $element Element to visit.
     *
     * @return Xcallable
     */
    public function visitOperator(AST\Operator $element)
    {
        $xcallable = $this->getOperator($element->getName());

        $arguments = array_map(function ($argument) {
            return $argument->accept($this);
        }, $element->getArguments());

        return $xcallable->distributeArguments($arguments);
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
        $this->operators[$operator] = $transformer;

        return $this;
    }

    /**
     * Check if an operator exists.
     *
     * @param string $operator Operator.
     *
     * @return bool
     */
    public function operatorExists($operator)
    {
        return array_key_exists($operator, $this->operators) === true;
    }

    /**
     * Get an operator.
     *
     * @param string $operator Operator.
     *
     * @return Xcallable
     */
    protected function getOperator($operator)
    {
        if (!$this->operatorExists($operator)) {
            throw new OperatorNotFoundException($operator, sprintf('Operator "%s" does not exist.', $operator));
        }

        $handle = &$this->operators[$operator];

        if (!$handle instanceof Xcallable) {
            $handle = xcallable($handle);
        }

        return $this->operators[$operator];
    }
}
