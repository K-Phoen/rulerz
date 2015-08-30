<?php

namespace RulerZ\Compiler\Target\Sql;

use Hoa\Ruler\Model as AST;

use RulerZ\Compiler\Target\GenericVisitor;
use RulerZ\Exception\OperatorNotFoundException;

/**
 * Base class for sql-related visitors.
 */
abstract class GenericSqlVisitor extends GenericVisitor
{
    /**
     * Allow star operator.
     *
     * @var bool
     */
    protected $allowStarOperator = true;

    /**
     * Constructor.
     *
     * @param array<callable> $operators The custom operators to register.
     * @param bool            $allowStarOperator Whether to allow the star operator or not (ie: implicit support of unknown operators).
     */
    public function __construct(array $operators = [], $allowStarOperator = true)
    {
        parent::__construct($operators);

        $this->allowStarOperator = (bool) $allowStarOperator;
    }

    /**
     * {@inheritDoc}
     */
    public function visitScalar(AST\Bag\Scalar $element, &$handle = null, $eldnah = null)
    {
        $value = parent::visitScalar($element, $handle, $eldnah);

        return is_numeric($value) ? $value : sprintf("'%s'", $value);
    }

    /**
     * {@inheritDoc}
     */
    public function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null)
    {
        $array = parent::visitArray($element, $handle, $eldnah);

        return sprintf('(%s)', implode(', ', $array));
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
    public function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null)
    {
        try {
            return parent::visitOperator($element, $handle, $eldnah);
        } catch (OperatorNotFoundException $e) {
            if (!$this->allowStarOperator) {
                throw $e;
            }
        }

        $arguments = array_map(function ($argument) use (&$handle, $eldnah) {
            return $argument->accept($this, $handle, $eldnah);
        }, $element->getArguments());

        return sprintf('%s(%s)', $element->getName(), implode(', ', $arguments));
    }

    /**
     * Define the built-in operators.
     */
    protected function defineBuiltInOperators()
    {
        $this->setInlineOperator('and',  function ($a, $b) { return sprintf('(%s AND %s)', $a, $b); });
        $this->setInlineOperator('or',   function ($a, $b) { return sprintf('(%s OR %s)', $a, $b); });
        $this->setInlineOperator('not',  function ($a)     { return sprintf('NOT (%s)', $a); });
        $this->setInlineOperator('=',    function ($a, $b) { return sprintf('%s = %s', $a, $b); });
        $this->setInlineOperator('!=',   function ($a, $b) { return sprintf('%s != %s', $a, $b); });
        $this->setInlineOperator('>',    function ($a, $b) { return sprintf('%s > %s', $a,  $b); });
        $this->setInlineOperator('>=',   function ($a, $b) { return sprintf('%s >= %s', $a,  $b); });
        $this->setInlineOperator('<',    function ($a, $b) { return sprintf('%s < %s', $a,  $b); });
        $this->setInlineOperator('<=',   function ($a, $b) { return sprintf('%s <= %s', $a,  $b); });
        $this->setInlineOperator('in',   function ($a, $b) { return sprintf('%s IN %s', $a, $b[0] === '(' ? $b : '('.$b.')'); });
        $this->setInlineOperator('like', function ($a, $b) { return sprintf('%s LIKE %s', $a, $b); });
    }
}
