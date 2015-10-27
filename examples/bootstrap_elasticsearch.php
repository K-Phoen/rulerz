<?php

require __DIR__.'/../vendor/autoload.php';

$client = new Elasticsearch\Client([
    //'hosts' => ['172.18.42.1']
    'hosts' => ['localhost']
]);

// compiler
$compiler = new \RulerZ\Compiler\EvalCompiler(new \RulerZ\Parser\HoaParser());

// RulerZ engine
$rulerz = new \RulerZ\RulerZ(
    $compiler, [
        new \RulerZ\Compiler\Target\Elasticsearch\ElasticsearchVisitor(),
    ]
);

return [$client, $rulerz];
