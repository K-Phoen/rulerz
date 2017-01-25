<?php

namespace RulerZ\Target\Eloquent;

use Hoa\Ruler\Model as AST;

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

    public function __construct(Context $context, OperatorsDefinitions $operators, $allowStarOperator = true, $allowEloquentBuilderAsQuery = false)
    {
        parent::__construct($context, $operators, $allowStarOperator);

        $this->allowEloquentBuilderAsQuery = (bool) $allowEloquentBuilderAsQuery;
    }

    /**
     * @inheritDoc
     */
    public function getCompilationData()
    {
        return [
            'allowEloquentBuilderAsQuery ' => $this->allowEloquentBuilderAsQuery,
        ];
    }
}
