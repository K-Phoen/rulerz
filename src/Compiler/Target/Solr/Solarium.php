<?php

namespace RulerZ\Compiler\Target\Solr;

use Solarium\Client as SolariumClient;

use RulerZ\Compiler\Context;
use RulerZ\Compiler\Visitor\Solr\SolariumVisitor;
use RulerZ\Compiler\Target\AbstractCompilationTarget;

class Solarium extends AbstractCompilationTarget
{
    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof SolariumClient;
    }

    /**
     * @inheritDoc
     */
    protected function createVisitor(Context $context)
    {
        return new SolariumVisitor($this->getOperators(), $this->getInlineOperators());
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Solr\SolariumFilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }
}
