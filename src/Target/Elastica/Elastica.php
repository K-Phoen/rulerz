<?php

declare(strict_types=1);

namespace RulerZ\Target\Elastica;

use Elastica\Search;
use Elastica\SearchableInterface;
use FOS\ElasticaBundle\Finder\TransformedFinder;

use RulerZ\Compiler\Context;
use RulerZ\Executor\Elasticsearch\ElasticaFilterTrait;
use RulerZ\Executor\Polyfill\FilterBasedSatisfaction;
use RulerZ\Target\AbstractCompilationTarget;
use RulerZ\Target\GenericElasticsearchVisitor;
use RulerZ\Target\Operators\Definitions;
use RulerZ\Target\Operators\GenericElasticsearchDefinitions;

class Elastica extends AbstractCompilationTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, string $mode): bool
    {
        return $target instanceof SearchableInterface || $target instanceof TransformedFinder || $target instanceof Search;
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
            ElasticaFilterTrait::class,
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
