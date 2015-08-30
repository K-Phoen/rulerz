<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

error_reporting(E_ALL);

require './vendor/autoload.php';

$capsule = new Capsule();

$capsule->addConnection([
    'driver'    => 'sqlite',
    'database'  => __DIR__.'/rulerz.db',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

//$capsule->schema()->create('player', function($table) {
//    $table->string('pseudo');
//    $table->string('name');
//    $table->integer('points');
//    $table->timestamps();
//});

class Player extends Model
{
}

$users = Player::all();
var_dump(count($users));


// compiler
$compiler = new \RulerZ\Compiler\EvalCompiler(new \RulerZ\Parser\HoaParser());

// RulerZ engine
$rulerz = new \RulerZ\RulerZ(
    $compiler, [
        new \RulerZ\Compiler\Target\Sql\EloquentVisitor(),
    ]
);
$qb = Player::query();
$rule  = 'points > :points AND LENGTH(pseudo) > 3';
$parameters = [
    'points' => 30,
];

var_dump($rulerz->filter($qb, $rule, $parameters));
