<?php

use RulerZ\Spec\Expr;
use Entity\Player;

list($entityManager, $rulerz) = require __DIR__.'/bootstrap.php';

// 1. Write a specification
$spec = Expr::andX(
    Expr::equals('gender', 'F'),
    Expr::moreThan('points', 3000)
);

// 2. Define a few targets to filter

// Like an array of arrays
$playersArr = [
    ['pseudo' => 'Joe',   'fullname' => 'Joe la frite',             'gender' => 'M', 'points' => 2500],
    ['pseudo' => 'Moe',   'fullname' => 'Moe, from the bar!',       'gender' => 'M', 'points' => 1230],
    ['pseudo' => 'Alice', 'fullname' => 'Alice, from... you know.', 'gender' => 'F', 'points' => 9001],
];

// or an array of objects
$playersObj = [
    new Player('Joe', 'Joe la frite', 'M', 2500),
    new Player('Moe', 'Moe, from the bar!', 'M', 1230),
    new Player('Alice', 'Alice, from... you know.', 'F', 9001),
];

// 3. Filter the targets
var_dump(iterator_to_array($rulerz->filterSpec($playersArr, $spec)));
var_dump(iterator_to_array($rulerz->filterSpec($playersObj, $spec)));

// 4. check if an existing target satisfies the spec
var_dump($rulerz->satisfiesSpec($playersObj[1], $spec));
