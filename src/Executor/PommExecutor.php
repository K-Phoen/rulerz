<?php

namespace RulerZ\Executor;

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
        $searchQuery = $this->buildSearchQuery($rule);

        return $target->findWhere($searchQuery, $parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        if (!is_object($target)) {
            return false;
        }

        $usedTraits = class_uses($target);

        return in_array('PommProject\ModelManager\Model\ModelTrait\WriteQueries', $usedTraits)
            || in_array('PommProject\ModelManager\Model\ModelTrait\ReadQueries', $usedTraits);
    }

    /**
     * Builds the search query for the given rule.
     *
     * @param Model $rule The rule to apply.
     *
     * @return string The search.
     */
    private function buildSearchQuery(Model $rule)
    {
        $searchBuilder = new PommVisitor();

        foreach ($this->getOperators() as $name => $callable) {
            $searchBuilder->setOperator($name, $callable);
        }

        return $searchBuilder->visit($rule);
    }
}
