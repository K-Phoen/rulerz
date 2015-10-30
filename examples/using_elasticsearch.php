<?php

list($client, $rulerz) = require __DIR__ . '/bootstrap/bootstrap_elasticsearch.php';

// 1. Write a rule.
$rule  = 'gender = "f" and points > :points';

// 2. Define the execution context
$context = [
    'index' => 'rulerz_tests',
    'type'  => 'player'
];

// 3. Enjoy!
$parameters = [
    'points' => 3000,
    'gender' => 'f',
];

$players = $rulerz->filter($client, $rule, $parameters, $context);

var_dump($players);
