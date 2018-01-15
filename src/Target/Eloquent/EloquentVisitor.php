<?php

namespace RulerZ\Target\Eloquent;

use RulerZ\Model;
use RulerZ\Compiler\Context;
use RulerZ\Target\GenericSqlVisitor;
use RulerZ\Target\Operators\Definitions as OperatorsDefinitions;

class EloquentVisitor extends GenericSqlVisitor
{
    /**
     * Allow eloquent builder as query.
     *
     * @var bool
     */
    protected $allowEloquentBuilderAsQuery = false;

    /**
     * Preserve positional parameters.
     *
     * @var bool
     */
    protected $preservePositionalParameters = false;

    /**
     * @param bool $allowEloquentBuilderAsQuery Whether to allow the execution target to be eloquent builder instead of query builder.
     */
    public function __construct(
        Context $context,
        OperatorsDefinitions $operators,
        $allowStarOperator = true,
        $allowEloquentBuilderAsQuery = false,
        $preservePositionalParameters = false
    ) {
        parent::__construct($context, $operators, $allowStarOperator);

        $this->allowEloquentBuilderAsQuery = (bool) $allowEloquentBuilderAsQuery;
        $this->preservePositionalParameters = (bool) $preservePositionalParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        return $this->preservePositionalParameters ? '?' : ':'.$element->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompilationData()
    {
        return [
            'allowEloquentBuilderAsQuery ' => $this->allowEloquentBuilderAsQuery,
        ];
    }
}
