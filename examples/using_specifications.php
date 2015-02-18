<?php

use Entity\User;
use RulerZ\Executor\ArrayExecutor;
use RulerZ\Executor\DoctrineQueryBuilderExecutor;
use RulerZ\Interpreter\HoaInterpreter;

$entityManager = require 'bootstrap.php';

$rulerz = new RulerZ\RulerZ(
    new HoaInterpreter(), [
        new DoctrineQueryBuilderExecutor(),
        new ArrayExecutor(),
    ]
);

// 1. Write a specification
$spec = new RulerZ\Spec\AndX([ // guests having at least 30 points
    new SampleSpecs\GuestUsers(),
    new SampleSpecs\MinScore(30),
]);

// 2. Define a few targets to filter

// a QueryBuilder
$usersQb = $entityManager
    ->createQueryBuilder()
    ->select('u')
    ->from('Entity\User', 'u');

// or an array of arrays
$usersArr = [
    ['name' => 'Joe', 'group' => 'guest', 'points' => 40],
    ['name' => 'Moe', 'group' => 'guest', 'points' => 20],
    ['name' => 'Al',  'group' => 'guest', 'points' => 40],
];

// or an array of objects
$usersObj = [
    new User('Joe', 'guest', 40),
    new User('Moe', 'guest', 20),
    new User('Al',  'guest', 40),
];

// 3. Filter the targets
var_dump($rulerz->filterSpec($usersQb, $spec));
var_dump($rulerz->filterSpec($usersArr, $spec));
var_dump($rulerz->filterSpec($usersObj, $spec));


// 4. check if an existing target satisfies the spec
var_dump($rulerz->satisfiesSpec($usersObj[1], $spec));
var_dump($rulerz->satisfiesSpec($usersQb, $spec));
