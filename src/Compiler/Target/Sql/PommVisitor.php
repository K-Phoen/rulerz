<?php

namespace RulerZ\Compiler\Target\Sql;

use Hoa\Ruler\Model as AST;
use PommProject\ModelManager\Model\Model as PommModel;

use RulerZ\Model;

class PommVisitor extends GenericSqlVisitor
{
    /**
     * @inheritDoc
     */
    public function supports($target, $mode)
    {
        // we make the assumption that pomm models use at least the
        // \PommProject\ModelManager\Model\ModelTrait\ReadQueries trait
        return $target instanceof PommModel;
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Pomm\FilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        $handle[] = sprintf('$parameters["%s"]', $element->getName());

        // make it a placeholder
        return '$*';
    }

    /**
     * {@inheritDoc}
     */
    public function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null)
    {
        $parameters = [];
        $operator   = $element->getName();
        $sql        = parent::visitOperator($element, $parameters, $eldnah);

        if (in_array($operator, ['and', 'or'])) {
            return $sql;
        }

        return sprintf('(new \PommProject\Foundation\Where("%s", [%s]))', $sql, implode(', ', $parameters));
    }

    /**
     * Define the built-in operators.
     */
    protected function defineBuiltInOperators()
    {
        parent::defineBuiltInOperators();

        // just override these two
        $this->setInlineOperator('and', function ($a, $b) {
            return sprintf('%s->andWhere(%s)', $a, $b);
        });
        $this->setInlineOperator('or', function ($a, $b) {
            return sprintf('%s->orWhere(%s)', $a, $b);
        });
    }
}
