<?php

use RulerZ\Executor\ArrayExecutor;
use RulerZ\Executor\DoctrineQueryBuilderExecutor;
use RulerZ\Interpreter\HoaInterpreter;

$entityManager = require 'bootstrap_doctrine.php';

$rulerz = new RulerZ\RulerZ(
    new HoaInterpreter(), [
        new DoctrineQueryBuilderExecutor([
            'length' => function ($arg) {
                return sprintf('LENGTH(%s)', $arg); // transform the call to its DQL equivalent
            }
        ]),
        new ArrayExecutor([
            'length' => 'strlen', // in plain PHP, just use the strlen function
        ]),
    ]
);

// 1. Write a rule.
$rule  = 'points > :points and length(name) > 2';

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

// 3. Enjoy!
$parameters = [
    'points' => 30,
];

var_dump($rulerz->filter($usersQb, $rule, $parameters));
var_dump($rulerz->filter($usersArr, $rule, $parameters));


// check if a target satisfies a rule
var_dump($rulerz->satisfies($usersArr[1], $rule, $parameters));
var_dump($rulerz->satisfies($usersQb, $rule, $parameters));
