<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Entity\User;
use Executor\ArrayExecutor;
use Executor\DoctrineQueryBuilderExecutor;

$entityManager = require 'bootstrap.php';

$rulerz = new RulerZ([
    new ArrayExecutor([
        'length' => 'strlen',
    ]),
    new DoctrineQueryBuilderExecutor(),
]);

// 1. Write a rule.
$rule  = 'group in :groups and points > :points and length(name) > 2';

// 2. Filter a collection
$usersQb = $entityManager
    ->createQueryBuilder()
    ->select('u')
    ->from('Entity\User', 'u')
    ->orderBy('u.name')
    ->setFirstResult(0)
    ->setMaxResults(5);

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
];

// 3. Enjoy!
$parameters = array(
    'points' => 30,
    'groups' => ['customer', 'guest'],
);

var_dump($rulerz->filter($usersQb, $rule, $parameters));
var_dump($rulerz->filter($usersArr, $rule, $parameters));
var_dump($rulerz->filter($usersObj, $rule, $parameters));
