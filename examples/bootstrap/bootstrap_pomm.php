<?php

use PommProject\Foundation\Pomm;

use RulerZ\RulerZ;
use RulerZ\Parser\HoaParser;

require __DIR__.'/bootstrap_general.php';

$pomm = new Pomm(['test_rulerz' => [
    'dsn'                   => sprintf('pgsql://%s:%s@%s:%d/%s', $_ENV['POSTGRES_USER'], $_ENV['POSTGRES_PASSWD'], $_ENV['POSTGRES_HOST'], $_ENV['POSTGRES_PORT'], $_ENV['POSTGRES_DB']),
    'class:session_builder' => '\PommProject\ModelManager\SessionBuilder'
]]);

// compiler
$compiler = new \RulerZ\Compiler\EvalCompiler(new HoaParser());

// compiled RulerZ
$rulerz = new RulerZ(
    $compiler, [
        new \RulerZ\Compiler\Target\Sql\PommVisitor(),
    ]
);

return [$pomm, $rulerz];
