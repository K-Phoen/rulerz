<?php

use Entity\Player;

list($entityManager, $rulerz) = require 'bootstrap_doctrine.php';

// 1. Write a rule.
$rule  = 'gender = :gender and points > :points';

// 2. Define a few targets to filter

// a QueryBuilder
$usersQb = $entityManager
    ->createQueryBuilder()
    ->select('p')
    ->from('Entity\Player', 'p');

// or an array of arrays
$usersArr = [
    ['pseudo' => 'Joe',   'fullname' => 'Joe la frite',             'gender' => 'M', 'points' => 2500],
    ['pseudo' => 'Moe',   'fullname' => 'Moe, from the bar!',       'gender' => 'M', 'points' => 1230],
    ['pseudo' => 'Alice', 'fullname' => 'Alice, from... you know.', 'gender' => 'F', 'points' => 9001],
];

// or an array of objects
$usersObj = [
    new Player('Joe',   'Joe la frite', 'M', 40, 2500),
    new Player('Moe',   'Moe, from the bar!', 'M', 55, 1230),
    new Player('Alice', 'Alice, from... you know.', 'F', 27, 9001),
];

// 3. Enjoy!
$parameters = [
    'points' => 3000,
    'gender' => 'F',
];

var_dump($rulerz->filter($usersQb, $rule, $parameters));
var_dump($rulerz->filter($usersArr, $rule, $parameters));
var_dump($rulerz->filter($usersObj, $rule, $parameters));


// check if a target satisfies a rule
var_dump($rulerz->satisfies($usersArr[0], $rule, $parameters));
var_dump($rulerz->satisfies($usersObj[2], $rule, $parameters));
var_dump($rulerz->satisfies($usersQb, $rule, $parameters));
