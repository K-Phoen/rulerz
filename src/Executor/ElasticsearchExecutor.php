<?php

namespace RulerZ\Executor;

use Elasticsearch\Client;
use Hoa\Ruler\Model;

use RulerZ\Visitor\ElasticsearchVisitor;

/**
 * Execute a rule on an elasticsearch client.
 */
class ElasticsearchExecutor implements Executor
{
    /**
     * @var array A list of additionnal operators.
     */
    private $operators = [];

    public function __construct(array $operators = [])
    {
        $this->registerOperators($operators);
    }

    /**
     * {@inheritDoc}
     */
    public function filter($target, Model $rule, array $parameters = [])
    {
        if (empty($parameters['index']) || empty($parameters['type'])) {
            throw new \BadMethodCallException('Parameters "index" and "type" are mandatory for the ElasticsearchExecutor"');
        }

        list($index, $type) = [$parameters['index'], $parameters['type']];
        unset($parameters['index'], $parameters['type']);

        $searchQuery = $this->buildSearchQuery($rule, $parameters);

        return $target->search([
            'index' => $index,
            'type'  => $type,
            'body'  => ['query' => $searchQuery],
        ]);
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
        return $target instanceof Client;
    }

    /**
     * {@inheritDoc}
     */
    public function registerOperators(array $operators)
    {
        $this->operators = array_merge($this->operators, $operators);
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

        foreach ($this->operators as $name => $callable) {
            $searchBuilder->setOperator($name, $callable);
        }

        return $searchBuilder->visit($rule);
    }
}
