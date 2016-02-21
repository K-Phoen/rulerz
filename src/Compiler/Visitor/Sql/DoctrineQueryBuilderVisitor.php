<?php

namespace RulerZ\Compiler\Visitor\Sql;

use Hoa\Ruler\Model as AST;
use RulerZ\Compiler\Context;
use RulerZ\Exception;
use RulerZ\Model;

class DoctrineQueryBuilderVisitor extends GenericSqlVisitor
{
    /**
     * @var DoctrineAutoJoin
     */
    private $autoJoin;

    public function __construct(Context $context, array $operators = [], array $inlineOperators = [], $allowStarOperator = true)
    {
        parent::__construct($context, $operators, $inlineOperators, $allowStarOperator);

        $this->context = $context;
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
