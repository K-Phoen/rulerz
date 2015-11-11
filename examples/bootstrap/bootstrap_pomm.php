<?php

use \PommProject\Foundation\Pomm;

require __DIR__.'/bootstrap_general.php';

return new Pomm(['test_rulerz' => [
    'dsn'                   => sprintf('pgsql://%s:%s@%s:%d/%s', $_ENV['POSTGRES_USER'], $_ENV['POSTGRES_PASSWD'], $_ENV['POSTGRES_HOST'], $_ENV['POSTGRES_PORT'], $_ENV['POSTGRES_DB']),
    'class:session_builder' => '\PommProject\ModelManager\SessionBuilder'
]]);
