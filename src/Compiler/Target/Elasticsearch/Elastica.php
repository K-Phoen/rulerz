<?php

namespace RulerZ\Compiler\Target\Elasticsearch;

use Elastica\Search;
use Elastica\SearchableInterface;
use FOS\ElasticaBundle\Finder\TransformedFinder;

use RulerZ\Compiler\Context;
use RulerZ\Compiler\Target\AbstractCompilationTarget;
use RulerZ\Compiler\Visitor\Elasticsearch\GenericElasticsearchVisitor;

class Elastica extends AbstractCompilationTarget
{
    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof SearchableInterface || $target instanceof TransformedFinder || $target instanceof Search;
    }

    /**
     * @inheritDoc
     */
    protected function createVisitor(Context $context)
    {
        return new GenericElasticsearchVisitor($this->getOperators(), $this->getInlineOperators());
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Elasticsearch\ElasticaFilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }
}
