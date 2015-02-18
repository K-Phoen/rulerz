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

// 1. Write a rule.
$rule  = 'group in :groups and points > :points';

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

// 3. Enjoy!
$parameters = [
    'points' => 30,
    'groups' => ['customer', 'guest'],
];

var_dump($rulerz->filter($usersQb, $rule, $parameters));
var_dump($rulerz->filter($usersArr, $rule, $parameters));
var_dump($rulerz->filter($usersObj, $rule, $parameters));


// check if a target satisfies a rule
var_dump($rulerz->satisfies($usersObj[1], $rule, $parameters));
var_dump($rulerz->satisfies($usersQb, $rule, $parameters));
