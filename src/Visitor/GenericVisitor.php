<?php

namespace RulerZ\Visitor;

use Hoa\Core\Consistency\Xcallable;
use Hoa\Ruler\Model as AST;
use Hoa\Visitor\Element as VisitorElement;
use Hoa\Visitor\Visit as Visitor;

use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Model;

/**
 * Generic visitor intended to be extended.
 */
abstract class GenericVisitor implements RuleVisitor
{
    /**
     * List of operators.
     *
     * @var array
     */
    private $operators = [];

    /**
     * {@inheritDoc}
     */
    public function visit(VisitorElement $element, &$handle = null, $eldnah = null)
    {
        if ($element instanceof AST\Model) {
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
        $xcallable = $this->getOperator($element->getName());

        $arguments = array_map(function ($argument) use (&$handle, $eldnah) {
            return $argument->accept($this, $handle, $eldnah);
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
