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
     * {@inheritdoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof Client;
    }

    /**
     * {@inheritdoc}
     */
    protected function createVisitor(Context $context)
    {
        return new GenericElasticsearchVisitor($this->getOperators());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Elasticsearch\ElasticsearchFilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return GenericElasticsearchDefinitions::create(parent::getOperators());
    }
}
