<?php

declare(strict_types=1);

use Entity\Player;

/** @var \RulerZ\RulerZ $rulerz */
$rulerz = require __DIR__.'/bootstrap.php';

// 1. Write a rule.
$rule = 'gender = :gender and points > :points';

// 2. Define a few targets to filter

// like an array of arrays
$playersArr = [
    ['pseudo' => 'Joe',   'fullname' => 'Joe la frite',             'gender' => 'M', 'points' => 2500],
    ['pseudo' => 'Moe',   'fullname' => 'Moe, from the bar!',       'gender' => 'M', 'points' => 1230],
    ['pseudo' => 'Alice', 'fullname' => 'Alice, from... you know.', 'gender' => 'F', 'points' => 9001],
];

// or an array of objects
$playersObj = [
    new Player('Joe', 'Joe la frite', 'M', 2500, null, new \DateTime('2020-01-02')),
    new Player('Moe', 'Moe, from the bar!', 'M', 1230, null, new DateTime('2005-01-04')),
    new Player('Alice', 'Alice, from... you know.', 'F', 9001, null, new DateTime('2007-01-07')),
];

// 3. Enjoy!
$parameters = [
    'points' => 3000,
    'gender' => 'F',
];

$players = $rulerz->filter($playersObj, $rule, $parameters);
var_dump(array_map(function ($player) {
    return $player->pseudo;
}, iterator_to_array($players)));
var_dump(iterator_to_array($rulerz->filter($playersArr, $rule, $parameters)));
var_dump(iterator_to_array($rulerz->filter($playersObj, $rule, $parameters)));

// check if a target satisfies a rule
var_dump($rulerz->satisfies($playersArr[0], $rule, $parameters));
var_dump($rulerz->satisfies($playersObj[2], $rule, $parameters));
