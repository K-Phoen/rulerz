<?php

namespace RulerZ\Executor;

use PommProject\ModelManager\Model\Model as ModelQuery;
use Hoa\Ruler\Model;

use RulerZ\Visitor\PommVisitor;

/**
 * Execute a rule on Pomm.
 */
class PommExecutor implements ExtendableExecutor
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

        return $target->findWhere($searchQuery, $parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof ModelQuery;
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
        $searchBuilder = new PommVisitor($parameters);

        foreach ($this->getOperators() as $name => $callable) {
            $searchBuilder->setOperator($name, $callable);
        }

        return $searchBuilder->visit($rule);
    }
}
