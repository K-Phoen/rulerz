<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Entity\Player;

class ArrayContext implements Context
{
    private $rulerz;
    private $dataset;
    private $parameters = [];
    private $results;

    /**
     * @Given RulerZ is configured
     */
    public function rulerzIsConfigured()
    {
        // compiler
        $compiler = new \RulerZ\Compiler\EvalCompiler(new \RulerZ\Parser\HoaParser());

        // RulerZ engine
        $this->rulerz = new \RulerZ\RulerZ(
            $compiler, [
                new \RulerZ\Compiler\Target\ArrayVisitor([
                    'length' => 'strlen'
                ]),
            ]
        );
    }

    /**
     * @When I define the parameters:
     */
    public function iDefineTheParameters(TableNode $parameters)
    {
        $this->parameters = $parameters->getRowsHash();
    }

    /**
     * @When I filter the dataset with the rule:
     */
    public function iFilterTheDatasetWithTheRule(PyStringNode $rule)
    {
        $this->results = $this->rulerz->filter($this->dataset, (string) $rule, $this->parameters);

        $this->parameters = [];
    }

    /**
     * @When I use the array of arrays dataset
     */
    public function iFilterTheArrayOfArraysDatasetWithTheRule()
    {
        $this->dataset = $this->getArrayOfObjectsDataset();
    }

    /**
     * @When I use the array of objects dataset
     */
    public function iFilterTheArrayOfObjectsDatasetWithTheRule()
    {
        $this->dataset = $this->getArrayOfObjectsDataset();
    }

    /**
     * @Then I should have the following results:
     */
    public function iShouldHaveTheFollowingResults(TableNode $table)
    {
        if (count($table->getHash()) !== count($this->results)) {
            throw new \RuntimeException(sprintf("Expected %d results, got %d. Expected:\n%s\nGot:\n%s", count($table->getHash()), count($this->results), $table, var_export($this->results, true)));
        }

        foreach ($table as $row) {
            foreach ($this->results as $result) {
                $objectResult = is_array($result) ? (object) $result : $result;

                if ($objectResult->pseudo === $row['pseudo']) {
                    return;
                }
            }

            throw new \RuntimeException(sprintf('Player "%s" not found in the results.', $row['pseudo']));
        }
    }

    private function getArrayOfArraysDataset()
    {
        return [
            ['pseudo' => 'Joe',      'fullname' => 'Joe la frite',      'gender' => 'M', 'age' => 34,  'points' => 2500],
            ['pseudo' => 'Bob',      'fullname' => 'Bob Morane',        'gender' => 'M', 'age' => 62,  'points' => 9001],
            ['pseudo' => 'Ada',      'fullname' => 'Ada Lovelace',      'gender' => 'F', 'age' => 175, 'points' => 10000],
            ['pseudo' => 'Kévin',    'fullname' => 'Yup, that is me.',  'gender' => 'M', 'age' => 24,  'points' => 100],
            ['pseudo' => 'Margaret', 'fullname' => 'Margaret Hamilton', 'gender' => 'F', 'age' => 78,  'points' => 5000],
            ['pseudo' => 'Alice',    'fullname' => 'Alice foo',         'gender' => 'F', 'age' => 30,  'points' => 175],
        ];
    }

    private function getArrayOfObjectsDataset()
    {
        return [
            new Player('Joe',      'Joe la frite',      'M', 34,  2500),
            new Player('Bob',      'Bob Morane',        'M', 62,  9001),
            new Player('Ada',      'Ada Lovelace',      'F', 175, 10000),
            new Player('Kévin',    'Yup, that is me.',  'M', 24,  100),
            new Player('Margaret', 'Margaret Hamilton', 'F', 78,  5000),
            new Player('Alice',    'Alice foo',         'F', 30,  175),
        ];
    }
}
