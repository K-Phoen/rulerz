<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Entity\User;
use Executor\ArrayExecutor;
use Executor\DoctrineQueryBuilderExecutor;

$entityManager = require 'bootstrap.php';

$rulerz = new RulerZ([
    new ArrayExecutor(),
    new DoctrineQueryBuilderExecutor(),
]);

// 1. Write a rule.
$rule  = 'group in ["customer", "guest"] and points > 30';

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
];

// or an array of objects
$usersObj = [
    new User('Joe', 'guest', 40),
    new User('Moe', 'guest', 20),
];

// 3. Enjoy!
var_dump($rulerz->filter($rule, $usersQb));
var_dump($rulerz->filter($rule, $usersArr));
var_dump($rulerz->filter($rule, $usersObj));
