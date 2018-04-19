<?php

declare(strict_types=1);

namespace RulerZ\Visitor;

use Hoa\Ruler\Model as AST;

class AccessCollectorVisitor extends Visitor
{
    /**
     * @var array
     */
    private $accesses = [];

    /**
     * {@inheritdoc}
     */
    public function getCompilationData(): array
    {
        return [
            'accesses' => $this->accesses,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function visitAccess(AST\Bag\Context $element, &$handle = null, $eldnah = null)
    {
        $this->accesses[] = $element;
    }

    /**
     * {@inheritdoc}
     */
    public function visitModel(AST\Model $element, &$handle = null, $eldnah = null)
    {
        parent::visitModel($element, $handle, $eldnah);

        return $this->accesses;
    }
}
