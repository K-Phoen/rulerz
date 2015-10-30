<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Entity\Player;

class ArrayContext extends BaseContext
{
    protected function getCompilationTarget()
    {
        return new \RulerZ\Compiler\Target\ArrayVisitor([
            'length' => 'strlen'
        ]);
    }
    protected function getDefaultDataset()
    {
        return $this->getArrayOfArraysDataset();
    }

    /**
     * @When I use the array of arrays dataset
     */
    public function iUseTheArrayOfArraysDataset()
    {
        $this->dataset = $this->getArrayOfObjectsDataset();
    }

    /**
     * @When I use the array of objects dataset
     */
    public function iUseTheArrayOfObjectsDataset()
    {
        $this->dataset = $this->getArrayOfObjectsDataset();
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
