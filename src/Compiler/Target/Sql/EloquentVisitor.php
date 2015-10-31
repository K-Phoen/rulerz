<?php

namespace RulerZ\Compiler\Target\Sql;

use Hoa\Ruler\Model as AST;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

use RulerZ\Model;

class EloquentVisitor extends GenericSqlVisitor
{
    /**
     * @inheritDoc
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder || $target instanceof EloquentBuilder;
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Eloquent\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
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
        return ':'.$element->getName();
    }
}
