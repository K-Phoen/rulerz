<?php

declare(strict_types=1);

use Entity\Player;
use Entity\Group;
use RulerZ\Test\BaseContext;

class ArrayContext extends BaseContext
{
    private $arrayDataset;

    private $objectDataset;

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
        ], [
            'inline_is_leap_year' => function ($date) {
                return sprintf("%s->format('L') === '1'", $date);
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

    private function getArrayOfArraysDataset(): array
    {
        if ($this->arrayDataset !== null) {
            return $this->arrayDataset;
        }

        $fixtures = json_decode(file_get_contents(__DIR__.'/../../../examples/fixtures.json'), true);
        $players = [];

        foreach ($fixtures['players'] as $player) {
            $players[] = array_merge($player, [
                'birthday' => new DateTime($player['birthday']),
            ]);
        }

        return $this->arrayDataset = $players;
    }

    private function getArrayOfObjectsDataset()
    {
        if ($this->objectDataset !== null) {
            return $this->objectDataset;
        }

        $fixtures = json_decode(file_get_contents(__DIR__.'/../../../examples/fixtures.json'), true);
        $groups = [];
        $players = [];

        foreach ($fixtures['groups'] as $slug => $data) {
            $groups[$slug] = new Group($data['name']);
        }

        foreach ($this->getArrayOfArraysDataset() as $data) {
            $players[] = new Player(
                $data['pseudo'],
                $data['fullname'],
                $data['gender'],
                $data['points'],
                $groups[$data['group']],
                $data['birthday']
            );
        }

        return $this->objectDataset = $players;
    }
}
