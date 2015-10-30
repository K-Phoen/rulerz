<?php

use \PommProject\Foundation\Pomm;

$loader = require __DIR__.'/../../vendor/autoload.php';
$loader->add(null, __DIR__);

return new Pomm(['my_db' => [
    'dsn'                   => 'pgsql://postgres:root@172.17.0.5:5432/postgres',
    'class:session_builder' => '\PommProject\ModelManager\SessionBuilder'
]]);
