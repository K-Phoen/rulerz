<?php

declare(strict_types=1);

namespace RulerZ\Target\Solarium;

use RulerZ\Target\AbstractCompilationTarget;
use RulerZ\Target\Operators\Definitions;
use RulerZ\Target\Operators\GenericSolrDefinitions;
use Solarium\Client as SolariumClient;

use RulerZ\Compiler\Context;

class Solarium extends AbstractCompilationTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, string $mode): bool
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
    public function getOperators(): Definitions
    {
        return GenericSolrDefinitions::create(parent::getOperators());
    }
}
