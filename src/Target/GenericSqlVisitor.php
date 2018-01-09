<?php

namespace RulerZ\Target;

use Hoa\Ruler\Model as AST;

use RulerZ\Compiler\Context;
use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Model;
use RulerZ\Target\Operators\CompileTimeOperator;
use RulerZ\Target\Operators\Definitions as OperatorsDefinitions;

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
     * @param bool $allowStarOperator Whether to allow the star operator or not (ie: implicit support of unknown operators).
     */
    public function __construct(Context $context, OperatorsDefinitions $operators, $allowStarOperator = true)
    {
        parent::__construct($operators);

        $this->context = $context;
        $this->allowStarOperator = (bool) $allowStarOperator;
    }

    /**
     * {@inheritdoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        $sql = parent::visitModel($element, $handle, $eldnah);

        if (is_string($sql)) {
            return '"' . $sql . '"';
        } elseif ($sql instanceof CompileTimeOperator) {
            return '"' . $sql->format(true) . '"';
        } else {
            return $sql->format(false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // make it a placeholder
        return ':'.$element->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        return $this->flattenAccessPath($element);
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray(AST\Bag\RulerArray $element, &$handle = null, $eldnah = null)
    {
        $array = parent::visitArray($element, $handle, $eldnah);

        return sprintf('(%s)', implode(', ', $array));
    }

    /**
     * {@inheritdoc}
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
}
