<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

use RulerZ\Executor\EloquentExecutor;
use RulerZ\Interpreter\HoaInterpreter;

error_reporting(E_ALL);

require './vendor/autoload.php';

$capsule = new Capsule();

$capsule->addConnection([
    'driver'    => 'sqlite',
    'database'  => ':memory:',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$capsule->schema()->create('users', function($table) {
    $table->increments('id');
    $table->string('name');
    $table->integer('points');
    $table->timestamps();
});

class User extends Model
{
}

$names  = ['John', 'Joe', 'Jane'];
$scores = [30, 40, 35];

foreach ($names as $i => $name) {
    $user = new User();
    $user->name = $name;
    $user->points = $scores[$i];
    $user->save();
}

$users = User::all();
var_dump(count($users));


// RulerZ!
$rulerz = new RulerZ\RulerZ(
    new HoaInterpreter(), [
        new EloquentExecutor(),
    ]
);

$qb = User::query();
$rule  = 'points > :points AND LENGTH(name) > 3';
$parameters = [
    'points' => 30,
];

var_dump($rulerz->filter($qb, $rule, $parameters));
