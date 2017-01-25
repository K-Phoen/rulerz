<?php

use Entity\Doctrine\Player;

list($entityManager, $rulerz) = require __DIR__.'/bootstrap/bootstrap_doctrine.php';

// 1. Write a specification
$spec = (new SampleSpecs\FemalePlayer())->andX(new SampleSpecs\MinScore(3000)); // female players having at least 3000 points

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
    new Player('Joe',   'Joe la frite',             'M', 40, 2500),
    new Player('Moe',   'Moe, from the bar!',       'M', 55, 1230),
    new Player('Alice', 'Alice, from... you know.', 'F', 27, 9001),
];

// 3. Filter the targets
var_dump($rulerz->filterSpec($playersQb, $spec));
var_dump($rulerz->filterSpec($playersArr, $spec));
var_dump($rulerz->filterSpec($playersObj, $spec));

// 4. check if an existing target satisfies the spec
var_dump($rulerz->satisfiesSpec($playersObj[1], $spec));
var_dump($rulerz->satisfiesSpec($playersQb, $spec));
