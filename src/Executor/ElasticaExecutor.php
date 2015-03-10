<?php

namespace RulerZ\Executor;

use Elastica\Search;
use Elastica\SearchableInterface as ElasticaSearchable;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Hoa\Ruler\Model;

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
    public function filter($target, Model $rule, array $parameters = [])
    {
        $searchQuery = $this->buildSearchQuery($rule, $parameters);

        if ($target instanceof ElasticaSearchable || $target instanceof Search) {
            return $target->search(['query' => $searchQuery]);
        }

        return $target->find(['query' => $searchQuery]);
    }

    /**
     * {@inheritDoc}
     */
    public function satisfies($target, Model $rule, array $parameters = [])
    {
        return count($this->filter($target, $rule, $parameters)) !== 0;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof ElasticaSearchable || $target instanceof TransformedFinder || $target instanceof Search;
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
        $searchBuilder = new ElasticsearchVisitor($parameters);

        foreach ($this->getOperators() as $name => $callable) {
            $searchBuilder->setOperator($name, $callable);
        }

        return $searchBuilder->visit($rule);
    }
}
