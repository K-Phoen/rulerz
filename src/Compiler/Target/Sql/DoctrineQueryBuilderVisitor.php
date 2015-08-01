<?php

namespace RulerZ\Compiler\Target\Sql;

use Doctrine\ORM\QueryBuilder;
use Hoa\Ruler\Model as AST;

use RulerZ\Model;

class DoctrineQueryBuilderVisitor extends GenericSqlVisitor
{
    /**
     * The root alias used by the query builder.
     */
    const ROOT_ALIAS_PLACEHOLDER = '@@_ROOT_ALIAS_@@';

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
            '\RulerZ\Executor\DoctrineQueryBuilder\FilterTrait',
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
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        return sprintf('%s.%s', self::ROOT_ALIAS_PLACEHOLDER, $element->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // make it a placeholder
        return ':' . $element->getName();
    }
}
