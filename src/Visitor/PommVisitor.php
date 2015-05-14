<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;
use PommProject\Foundation\Where;

use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Model;

class PommVisitor extends SqlVisitor
{
    use Polyfill\Parameters;

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        $handle[] = $this->lookupParameter($element->getName());

        return '$*';
    }

    /**
     * {@inheritDoc}
     */
    public function visitOperator(AST\Operator $element, &$handle = null, $eldnah = null)
    {
        try {
            $xcallable = $this->getOperator($element->getName());
        } catch (OperatorNotFoundException $e) {
            if (!$this->allowStarOperator) {
                throw $e;
            }

            $xcallable = $this->getStarOperator($element);
        }

        $parameters = [];

        $arguments = array_map(function ($argument) use (&$parameters) {
            return $argument->accept($this, $parameters);
        }, $element->getArguments());

        $sql = $xcallable->distributeArguments($arguments);

        if ($sql instanceof Where) {
            return $sql;
        }

        return new Where($sql, $parameters);
    }

    /**
     * Define the built-in operators.
     */
    protected function defineBuiltInOperators()
    {
        parent::defineBuiltInOperators();

        // just override these two
        $this->setOperator('and',  function ($a, $b) {
            return $a->andWhere($b);
        });
        $this->setOperator('or',   function ($a, $b) {
            return $a->orWhere($b);
        });
    }
}
