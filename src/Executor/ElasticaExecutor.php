<?php

namespace RulerZ\Executor;

use Elastica\Search;
use Elastica\SearchableInterface;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Hoa\Ruler\Model;

use RulerZ\Context\ExecutionContext;
use RulerZ\Visitor\ElasticsearchVisitor;

/**
 * Execute a rule on an Elastica client.
 */
class ElasticaExecutor implements ExtendableExecutor
{
    use Polyfill\ExtendableExecutor;
    use Polyfill\FilterBasedSatisfaction;

    /**
     * Constructs the Elasticsearch executor.
     *
     * @param array $operators A list of custom operators to register.
     */
    public function __construct(array $operators = [])
    {
        $this->registerOperators($operators);
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, Model $rule, array $parameters, ExecutionContext $context)
    {
        $searchQuery = $this->buildSearchQuery($rule, $parameters);

        if ($target instanceof SearchableInterface || $target instanceof Search) {
            return $target->search(['query' => $searchQuery]);
        }

        return $target->find(['query' => $searchQuery]);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof SearchableInterface || $target instanceof TransformedFinder || $target instanceof Search;
    }

    /**
     * Builds the search query for the given rule.
     *
     * @param Model $rule       The rule to apply.
     * @param array $parameters The search parameters.
     *
     * @return array The search.
     */
    private function buildSearchQuery(Model $rule, array $parameters)
    {
        $searchBuilder = new ElasticsearchVisitor();
        $searchBuilder->setOperators($this->getOperators());
        $searchBuilder->setParameters($parameters);

        return $searchBuilder->visit($rule);
    }
}
