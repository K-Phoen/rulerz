<?php

declare(strict_types=1);

namespace RulerZ\Target\Elasticsearch;

use Elasticsearch\Client;

use RulerZ\Compiler\Context;
use RulerZ\Executor\Elasticsearch\ElasticsearchFilterTrait;
use RulerZ\Executor\Polyfill\FilterBasedSatisfaction;
use RulerZ\Target\AbstractCompilationTarget;
use RulerZ\Target\GenericElasticsearchVisitor;
use RulerZ\Target\Operators\Definitions;
use RulerZ\Target\Operators\GenericElasticsearchDefinitions;

class Elasticsearch extends AbstractCompilationTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, string $mode): bool
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
            ElasticsearchFilterTrait::class,
            FilterBasedSatisfaction::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators(): Definitions
    {
        return GenericElasticsearchDefinitions::create(parent::getOperators());
    }
}
