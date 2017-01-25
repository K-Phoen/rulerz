<?php

namespace RulerZ\Target\DoctrineORM;

use Hoa\Ruler\Model as AST;

use RulerZ\Compiler\Context;
use RulerZ\Model;
use RulerZ\Target\GenericSqlVisitor;
use RulerZ\Target\Operators\Definitions as OperatorsDefinitions;

class DoctrineORMVisitor extends GenericSqlVisitor
{
    /**
     * @var DoctrineAutoJoin
     */
    private $autoJoin;

    public function __construct(Context $context, OperatorsDefinitions $operators, $allowStarOperator = true)
    {
        parent::__construct($context, $operators, $allowStarOperator);

        $this->autoJoin = new DoctrineAutoJoin($context['em'], $context['root_entities'], $context['root_aliases'], $context['joins']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompilationData()
    {
        return [
            'detectedJoins' => $this->autoJoin->getDetectedJoins(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        return $this->autoJoin->buildAccessPath($element);
    }

    /**
     * {@inheritdoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // placeholder for a positional parameters
        if (is_int($element->getName())) {
            return '?'.$element->getName();
        }

        // placeholder for a named parameter
        return ':'.$element->getName();
    }
}
