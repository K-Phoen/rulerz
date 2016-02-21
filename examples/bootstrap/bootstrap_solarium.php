<?php

require __DIR__.'/bootstrap_general.php';

// create a client instance
$client = new Solarium\Client([
    'endpoint' => [
        $_ENV['SOLR_CORE'] => [
            'host' => $_ENV['SOLR_HOST'],
            'port' => $_ENV['SOLR_PORT'],
            'path' => $_ENV['SOLR_PATH'],
            'core' => $_ENV['SOLR_CORE'],
        ]
    ]
]);

// compiler
$compiler = new \RulerZ\Compiler\Compiler(new \RulerZ\Compiler\EvalEvaluator());

// RulerZ engine
$rulerz = new \RulerZ\RulerZ(
    $compiler, [
        new \RulerZ\Compiler\Target\Solr\Solarium(),
    ]
);

return [$client, $rulerz];
