<?php

namespace RulerZ\Target\DoctrineORM;

use Hoa\Ruler\Model as AST;

use RulerZ\Compiler\Context;
use RulerZ\Exception;
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
     * @inheritDoc
     */
    public function getCompilationData()
    {
        return [
            'detectedJoins' => $this->autoJoin->getDetectedJoins(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        // simple attribute access
        if (count($element->getDimensions()) === 0) {
            return sprintf('%s.%s', $this->getRootAlias(), $element->getId());
        }

        return $this->autoJoin->buildAccessPath($element);
    }

    /**
     * {@inheritDoc}
     */
    public function visitParameter(Model\Parameter $element, &$handle = null, $eldnah = null)
    {
        // make it a placeholder
        return ':' . $element->getName();
    }

    private function getRootAlias()
    {
        return $this->context['root_aliases'][0];
    }
}
