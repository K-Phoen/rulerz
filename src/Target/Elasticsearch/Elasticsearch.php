<?php

namespace RulerZ\Target\Elasticsearch;

use Elasticsearch\Client;

use RulerZ\Compiler\Context;
use RulerZ\Target\AbstractCompilationTarget;
use RulerZ\Target\GenericElasticsearchVisitor;
use RulerZ\Target\Operators\GenericElasticsearchDefinitions;

class Elasticsearch extends AbstractCompilationTarget
{
    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof Client;
    }

    /**
     * @inheritDoc
     */
    protected function createVisitor(Context $context)
    {
        return new GenericElasticsearchVisitor($this->getOperators());
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Elasticsearch\ElasticsearchFilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getOperators()
    {
        return GenericElasticsearchDefinitions::create(parent::getOperators());
    }
}
