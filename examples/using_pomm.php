<?php

use RulerZ\RulerZ;
use RulerZ\Parser\HoaParser;

// Using a database of the French regions and towns
// http://pgfoundry.org/frs/?group_id=1000150&release_id=584

$pomm = require __DIR__ . '/bootstrap_pomm.php';

// compiler
$compiler = new \RulerZ\Compiler\EvalCompiler(new HoaParser());

// compiled RulerZ
$rulerz = new RulerZ(
    $compiler, [
        new \RulerZ\Compiler\Target\Sql\PommVisitor(),
    ]
);

$cities = $pomm['my_db']->getModel('\MyDb\PublicSchema\TownsModel');

$rule = 'name = :name';
var_dump($rule, $rulerz->filter($cities, $rule, [
    'name' => 'Gerzat'
])->get(0));

$rule = 'name = :name OR name = "Clermont-Ferrand"';
var_dump($rule, $rulerz->filter($cities, $rule, [
    'name' => 'Gerzat'
])->get(0));
