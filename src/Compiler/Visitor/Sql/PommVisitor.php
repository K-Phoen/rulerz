<?php

namespace RulerZ\Compiler\Visitor\Sql;

use Hoa\Ruler\Model as AST;

use RulerZ\Model;

class PommVisitor extends GenericSqlVisitor
{
    /**
     * {@inheritDoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        return $element->getExpression()->accept($this, $handle, $eldnah);
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

        if (in_array($operator, ['and', 'or', 'not'], true)) {
            return $sql;
        }

        if ($this->hasOperator($operator)) {
            return sprintf('(new \PommProject\Foundation\Where(%s, [%s]))', $sql, implode(', ', $parameters));
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
        $this->setInlineOperator('not', function ($a) {
            return sprintf('(new \PommProject\Foundation\Where("NOT(".%s->getElement() .")", %s->getValues()))', $a, $a);
        });
    }
}
