<?php

use \PommProject\Foundation\Pomm;

require __DIR__.'/bootstrap_general.php';

return new Pomm(['my_db' => [
    'dsn'                   => 'pgsql://postgres:root@172.17.0.5:5432/postgres',
    'class:session_builder' => '\PommProject\ModelManager\SessionBuilder'
]]);
