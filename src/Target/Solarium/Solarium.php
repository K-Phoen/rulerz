<?php

namespace RulerZ\Target\Solarium;

use RulerZ\Target\AbstractCompilationTarget;
use RulerZ\Target\Operators\GenericSolrDefinitions;
use Solarium\Client as SolariumClient;

use RulerZ\Compiler\Context;

class Solarium extends AbstractCompilationTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof SolariumClient;
    }

    /**
     * {@inheritdoc}
     */
    protected function createVisitor(Context $context)
    {
        return new SolariumVisitor($this->getOperators());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Solr\SolariumFilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return GenericSolrDefinitions::create(parent::getOperators());
    }
}
