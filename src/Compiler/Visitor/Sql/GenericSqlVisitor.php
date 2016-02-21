<?php

namespace RulerZ\Compiler\Visitor\Sql;

use Hoa\Ruler\Model as AST;

use RulerZ\Compiler\Context;
use RulerZ\Compiler\Target\Polyfill;
use RulerZ\Compiler\Visitor\GenericVisitor;
use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Model;

/**
 * Base class for sql-related visitors.
 */
class GenericSqlVisitor extends GenericVisitor
{
    use Polyfill\AccessPath;

    /**
     * @var Context
     */
    protected $context;

    /**
     * Allow star operator.
     *
     * @var bool
     */
    protected $allowStarOperator = true;

    /**
     * @param Context $context The compilation context.
     * @param array<callable> $operators A list of additional operators to register.
     * @param array<callable> $inlineOperators A list of additional inline operators to register.
     * @param bool $allowStarOperator Whether to allow the star operator or not (ie: implicit support of unknown operators).
     */
    public function __construct(Context $context, array $operators = [], array $inlineOperators = [], $allowStarOperator = true)
    {
        parent::__construct($operators, $inlineOperators);

        $this->context = $context;
        $this->allowStarOperator = (bool) $allowStarOperator;
    }

    /**
     * {@inheritDoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        $sql = parent::visitModel($element, $handle, $eldnah);

        return '"' . $sql . '"';
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // make it a placeholder
        return ':' . $element->getName();
    }

    /**
     * @inheritDoc
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        return $this->flattenAccessPath($element);
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
