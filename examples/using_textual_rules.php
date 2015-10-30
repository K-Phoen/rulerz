<?php

use Entity\Doctrine\Player;

list($entityManager, $rulerz) = require 'bootstrap_doctrine.php';

// 1. Write a rule.
$rule  = 'gender = :gender and points > :points';
$rule  = 'gender = :gender and group.role.name = "ROLE_ADMIN"';

// 2. Define a few targets to filter

// a QueryBuilder
$playersQb = $entityManager
    ->createQueryBuilder()
    ->select('p')
    ->from('Entity\Doctrine\Player', 'p');

// or an array of arrays
$playersArr = [
    ['pseudo' => 'Joe',   'fullname' => 'Joe la frite',             'gender' => 'M', 'points' => 2500],
    ['pseudo' => 'Moe',   'fullname' => 'Moe, from the bar!',       'gender' => 'M', 'points' => 1230],
    ['pseudo' => 'Alice', 'fullname' => 'Alice, from... you know.', 'gender' => 'F', 'points' => 9001],
];

// or an array of objects
$playersObj = [
    new Player('Joe',   'Joe la frite', 'M', 40, 2500),
    new Player('Moe',   'Moe, from the bar!', 'M', 55, 1230),
    new Player('Alice', 'Alice, from... you know.', 'F', 27, 9001),
];

// 3. Enjoy!
$parameters = [
//    'points' => 3000,
    'gender' => 'F',
];

$players = $rulerz->filter($playersQb, $rule, $parameters);
var_dump(array_map(function($player) {
    return $player->pseudo;
}, $players));
//var_dump($rulerz->filter($playersArr, $rule, $parameters));
//var_dump($rulerz->filter($playersObj, $rule, $parameters));


// check if a target satisfies a rule
//var_dump($rulerz->satisfies($playersQb, $rule, $parameters));
//var_dump($rulerz->satisfies($playersArr[0], $rule, $parameters));
//var_dump($rulerz->satisfies($playersObj[2], $rule, $parameters));
