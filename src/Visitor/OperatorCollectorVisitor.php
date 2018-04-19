<?php

declare(strict_types=1);

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;

class OperatorCollectorVisitor extends Visitor
{
    /**
     * @var array
     */
    private $operators = [];

    /**
     * {@inheritdoc}
     */
    public function getCompilationData(): array
    {
        return [
            'operators' => $this->operators,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        parent::visitModel($element, $handle, $eldnah);

        return $this->operators;
    }

    /**
     * {@inheritdoc}
     */
    public function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null)
    {
        parent::visitOperator($element, $handle, $eldnah);

        $this->operators[] = $element;
    }
}
