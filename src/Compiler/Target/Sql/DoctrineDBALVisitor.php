<?php

namespace RulerZ\Compiler\Target\Sql;

use Doctrine\DBAL\Query\QueryBuilder;
use Hoa\Ruler\Model as AST;

use RulerZ\Model;
use RulerZ\Compiler\Target\Polyfill;

class DoctrineDBALVisitor extends GenericSqlVisitor
{
    use Polyfill\AccessPath;

    /**
     * @inheritDoc
     */
    public function supports($target, $mode)
    {
        return $target instanceof QueryBuilder;
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\DoctrineDBAL\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        $dql = parent::visitModel($element, $handle, $eldnah);

        return '"' . $dql . '"';
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
}
