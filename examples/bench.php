<?php

use Webmozart\Expression\Expr;
use Webmozart\Expression\Logic\Disjunction;

list($entityManager, $rulerz) = require './examples/bootstrap_doctrine.php';

const REPETITIONS = 100000;

$dataset = [
    ['name' => 'Joe', 'group' => 'guest', 'points' => 50],
    ['name' => 'Moe', 'group' => 'guest', 'points' => 25],
    ['name' => 'Al',  'group' => 'guest', 'points' => 50],
    ['name' => 'Mom', 'group' => 'admin', 'points' => 500],
    ['name' => 'Dad', 'group' => 'admin', 'points' => 500],
];


$bench = new Hoa\Bench\Bench();


$rule = '(group = "guest" and points > 42) or (group = "admin" and points > 250)';
$rulerz_results = array_column($rulerz->filter($dataset, $rule), 'name');

$bench->{'kphoen/rulerz'}->start();

foreach (range(0, REPETITIONS) as $i) {
    $rulerz->filter($dataset, $rule);
}

$bench->{'kphoen/rulerz'}->stop();


// Expression
$expr = new Disjunction([
    Expr::greaterThan(42, 'points')->andEquals('guest', 'group'),
    Expr::equals('admin', 'group')->andGreaterThan(250, 'points')
]);

$expression_results = [];
foreach ($dataset as $row) {
    if ($expr->evaluate($row)) {
        $expression_results[] = $row['name'];
    }
}

$bench->{'webmozart/expression'}->start();
foreach (range(0, REPETITIONS) as $i) {
    $results = [];
    foreach ($dataset as $row) {
        if ($expr->evaluate($row)) {
            $results[] = $row;
        }
    }
}
$bench->{'webmozart/expression'}->stop();

if (!empty(array_diff($rulerz_results, $expression_results))) {
    echo 'Incoherency between rulerz and expression results.' . PHP_EOL;
    echo 'rulerz: ' . var_export($rulerz_results) . PHP_EOL;
    echo 'expression: ' . var_export($expression_results) . PHP_EOL;
    exit(1);
}

echo $bench;
