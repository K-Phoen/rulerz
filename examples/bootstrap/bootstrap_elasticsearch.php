<?php

require __DIR__.'/bootstrap_general.php';

$client = new Elasticsearch\Client([
    'hosts' => [
        sprintf('%s:%d', $_ENV['ELASTICSEARCH_HOST'], $_ENV['ELASTICSEARCH_PORT'])
    ],
]);

// compiler
$compiler = \RulerZ\Compiler\Compiler::create();

// RulerZ engine
$rulerz = new \RulerZ\RulerZ(
    $compiler, [
        new \RulerZ\Target\Elasticsearch\Elasticsearch(),
    ]
);

return [$client, $rulerz];
