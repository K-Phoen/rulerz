<?php

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;
use PommProject\Foundation\Where;

use RulerZ\Exception\OperatorNotFoundException;

class PommVisitor extends SqlVisitor
{
    /**
     * @var array $parameters The parameters used in the query.
     */
    private $parameters = [];

    /**
     * Constructor.
     *
     * @param bool $allowStarOperator Whether to allow the star operator or not (ie: implicit support of unknown operators).
     */
    public function __construct(array $parameters, $allowStarOperator = true)
    {
        parent::__construct($allowStarOperator);

        $this->parameters = $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $name = $element->getId();

        // parameter
        if ($name[0] === ':') {
            $handle[] = $this->parameters[substr($name, 1)];

            return '$*';
        }

        return $name;
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
