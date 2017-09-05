<?php

use Entity\Doctrine\Player;
use Entity\Doctrine\Group;

class ArrayContext extends BaseContext
{
    /**
     * {@inheritdoc}
     */
    protected function getCompilationTarget()
    {
        return new \RulerZ\Target\Native\Native([
            'length' => 'strlen',
        ]);
    }

    /**
     * {@inheritdoc}
     */
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
            ['pseudo' => 'Joe',       'fullname' => 'Joe la frite',      'gender' => 'M', 'age' => 34,  'points' => 2500],
            ['pseudo' => 'Bob',       'fullname' => 'Bob Morane',        'gender' => 'M', 'age' => 62,  'points' => 9001],
            ['pseudo' => 'Ada',       'fullname' => 'Ada Lovelace',      'gender' => 'F', 'age' => 175, 'points' => 10000],
            ['pseudo' => 'Kévin',     'fullname' => 'Yup, that is me.',  'gender' => 'M', 'age' => 24,  'points' => 100],
            ['pseudo' => 'Margaret',  'fullname' => 'Margaret Hamilton', 'gender' => 'F', 'age' => 78,  'points' => 5000],
            ['pseudo' => 'Alice',     'fullname' => 'Alice foo',         'gender' => 'F', 'age' => 30,  'points' => 175],
            ['pseudo' => 'Louise',    'fullname' => 'Louise foo',        'gender' => 'F', 'age' => 32,  'points' => 800],
            ['pseudo' => 'Francis',   'fullname' => 'Francis foo',       'gender' => 'M', 'age' => 30,  'points' => 345],
            ['pseudo' => 'John',      'fullname' => 'John foo',          'gender' => 'M', 'age' => 40,  'points' => 23],
            ['pseudo' => 'Arthur',    'fullname' => 'Arthur foo',        'gender' => 'M', 'age' => 25,  'points' => 200],
            ['pseudo' => 'Moon Moon', 'fullname' => 'Moon moon foo',     'gender' => 'D', 'age' => 7,   'points' => 300],
        ];
    }

    private function getArrayOfObjectsDataset()
    {
        $groups = [
            new Group('Océania'),
            new Group('Eurasia'),
            new Group('Estasia'),
        ];

        $birthDates = [
            new DateTime('2001-01-02'),
            new DateTime('2005-01-04'),
            new DateTime('2007-01-07'),
        ];

        $groupsMapping = [
            'Joe' => 2,
            'Bob' => 0,
            'Ada' => 1,
            'Kévin' => 1,
            'Margaret' => 2,
            'Alice' => 0,
            'Louise' => 1,
            'Francis' => 1,
            'John' => 1,
            'Arthur' => 1,
            'Moon Moon' => 1,
        ];

        $players = [];

        foreach ($this->getArrayOfArraysDataset() as $data) {
            $players[] = new Player(
                $data['pseudo'],
                $data['fullname'],
                $data['gender'],
                $data['age'],
                $data['points'],
                $groups[$groupsMapping[$data['pseudo']]],
                $birthDates[$groupsMapping[$data['pseudo']]]
            );
        }

        return $players;
    }
}
