<?php

namespace RulerZ\Target\Elastica;

use Elastica\Search;
use Elastica\SearchableInterface;
use FOS\ElasticaBundle\Finder\TransformedFinder;

use RulerZ\Compiler\Context;
use RulerZ\Target\AbstractCompilationTarget;
use RulerZ\Target\GenericElasticsearchVisitor;
use RulerZ\Target\Operators\GenericElasticsearchDefinitions;

class Elastica extends AbstractCompilationTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, $mode)
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
            '\RulerZ\Executor\Elasticsearch\ElasticaFilterTrait',
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
