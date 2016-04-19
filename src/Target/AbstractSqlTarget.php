<?php

namespace RulerZ\Target;

use RulerZ\Compiler\Context;
use RulerZ\Target\Operators\GenericSqlDefinitions;

abstract class AbstractSqlTarget extends AbstractCompilationTarget
{
    protected $allowStarOperator = true;

    public function __construct(array $operators = [], array $inlineOperators = [], $allowStarOperator = true)
    {
        parent::__construct($operators, $inlineOperators);

        $this->allowStarOperator = $allowStarOperator;
    }

    /**
     * @inheritDoc
     */
    protected function createVisitor(Context $context)
    {
        return new GenericSqlVisitor($context, $this->getOperators(), $this->allowStarOperator);
    }

    /**
     * @inheritDoc
     */
    public function getOperators()
    {
        return GenericSqlDefinitions::create(parent::getOperators());
    }
}
