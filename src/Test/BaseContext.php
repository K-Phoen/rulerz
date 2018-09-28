<?php

declare(strict_types=1);

namespace RulerZ\Test;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;

/**
 * @internal Meant to be used by the compilation target libraries.
 */
abstract class BaseContext implements Context
{
    /** @var \RulerZ\RulerZ */
    protected $rulerz;

    /** @var array */
    protected $parameters = [];

    /** @var array */
    protected $executionContext = [];

    /** @var mixed */
    protected $dataset;

    /** @var mixed */
    protected $results;

    /**
     * Returns the compilation target to be tested.
     *
     * @return \RulerZ\Compiler\CompilationTarget
     */
    abstract protected function getCompilationTarget();

    /**
     * Returns the default dataset to be filtered.
     *
     * @return mixed
     */
    abstract protected function getDefaultDataset();

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * @BeforeSuite
     */
    public static function prepare(BeforeSuiteScope $scope)
    {
        echo 'Current suite: '.$scope->getSuite()->getName();
    }

    /**
     * Will be called right after the construction of the context, useful to
     * initialize stuff before the tests are launched.
     */
    protected function initialize()
    {
    }

    /**
     * Create a default execution context that will be given to RulerZ when
     * filtering a dataset.
     *
     * @return mixed
     */
    protected function getDefaultExecutionContext()
    {
        return [];
    }

    /**
     * @Given RulerZ is configured
     */
    public function rulerzIsConfigured()
    {
        // compiler
        $compiler = new \RulerZ\Compiler\Compiler(new \RulerZ\Compiler\EvalEvaluator());

        // RulerZ engine
        $this->rulerz = new \RulerZ\RulerZ($compiler, [$this->getCompilationTarget()]);
    }

    /**
     * @When I define the parameters:
     */
    public function iDefineTheParameters(TableNode $parameters)
    {
        // named parameters
        if (count($parameters->getRow(0)) !== 1) {
            $this->parameters = $parameters->getRowsHash();

            return;
        }

        // positional parameters
        $this->parameters = array_map(function ($row) {
            return $row[0];
        }, $parameters->getRows());
    }

    /**
     * @When I use the default execution context
     */
    public function iUseTheDefaultExecutionContext()
    {
        $this->executionContext = $this->getDefaultExecutionContext();
    }

    /**
     * @When I use the default dataset
     */
    public function iUseTheDefaultDataset()
    {
        $this->dataset = $this->getDefaultDataset();
    }

    /**
     * @When I filter the dataset with the rule:
     */
    public function iFilterTheDatasetWithTheRule(PyStringNode $rule)
    {
        $this->results = $this->rulerz->filter($this->dataset, (string) $rule, $this->parameters, $this->executionContext);

        $this->parameters = [];
        $this->executionContext = [];
    }

    /**
     * @Then I should have the following results:
     */
    public function iShouldHaveTheFollowingResults(TableNode $table)
    {
        $results = iterator_to_array($this->results);

        if (count($table->getHash()) !== count($results)) {
            throw new \RuntimeException(sprintf("Expected %d results, got %d. Expected:\n%s\nGot:\n%s", count($table->getHash()), count($results), $table, var_export($results, true)));
        }

        foreach ($table as $row) {
            foreach ($results as $result) {
                $value = $this->fieldFromResult($result, 'pseudo');

                if ($value === $row['pseudo']) {
                    return;
                }
            }

            throw new \RuntimeException(sprintf('Player "%s" not found in the results.', $row['pseudo']));
        }
    }

    /**
     * Fetches a field from a result.
     * Meant to be overriden by other contexts.
     *
     * @param mixed $result
     * @param string $field
     *
     * @return mixed
     */
    protected function fieldFromResult($result, $field)
    {
        $objectResult = is_array($result) ? (object) $result : $result;

        return $objectResult->$field;
    }
}
