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
            'is_leap_year' => function (\DateTime $date) {
                return $date->format('L') === '1';
            },
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
            // Born on a leap year
            ['pseudo' => 'Joe',       'fullname' => 'Joe la frite',      'gender' => 'M', 'points' => 2500,  'birthday' => new DateTime('1924-03-02')],
            ['pseudo' => 'Bob',       'fullname' => 'Bob Morane',        'gender' => 'M', 'points' => 9001,  'birthday' => new DateTime('1995-10-02')],
            ['pseudo' => 'Ada',       'fullname' => 'Ada Lovelace',      'gender' => 'F', 'points' => 10000, 'birthday' => new DateTime('1997-10-02')],
            ['pseudo' => 'Kévin',     'fullname' => 'Yup, that is me.',  'gender' => 'M', 'points' => 100,   'birthday' => new DateTime('1999-10-02')],
            // Born on a leap year
            ['pseudo' => 'Margaret',  'fullname' => 'Margaret Hamilton', 'gender' => 'F', 'points' => 5000,  'birthday' => new DateTime('1936-08-17')],
            ['pseudo' => 'Alice',     'fullname' => 'Alice foo',         'gender' => 'F', 'points' => 175,   'birthday' => new DateTime('2001-10-02')],
            ['pseudo' => 'Louise',    'fullname' => 'Louise foo',        'gender' => 'F', 'points' => 800,   'birthday' => new DateTime('2002-10-02')],
            ['pseudo' => 'Francis',   'fullname' => 'Francis foo',       'gender' => 'M', 'points' => 345,   'birthday' => new DateTime('1998-10-02')],
            ['pseudo' => 'John',      'fullname' => 'John foo',          'gender' => 'M', 'points' => 23,    'birthday' => new DateTime('1987-10-02')],
            ['pseudo' => 'Arthur',    'fullname' => 'Arthur foo',        'gender' => 'M', 'points' => 200,   'birthday' => new DateTime('1989-10-02')],
            ['pseudo' => 'Moon Moon', 'fullname' => 'Moon moon foo',     'gender' => 'D', 'points' => 300,   'birthday' => new DateTime('1985-10-02')],
        ];
    }

    private function getArrayOfObjectsDataset()
    {
        $groups = [
            new Group('Océania'),
            new Group('Eurasia'),
            new Group('Estasia'),
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
                $data['points'],
                $groups[$groupsMapping[$data['pseudo']]],
                $data['birthday']
            );
        }

        return $players;
    }
}
