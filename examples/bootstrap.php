<?php

declare(strict_types=1);

$loader = require __DIR__.'/../vendor/autoload.php';

$rulerz = new \RulerZ\RulerZ(
    \RulerZ\Compiler\Compiler::create(), [
        new RulerZ\Target\Native\Native([
            'length' => function ($item) {
                return is_array($item) ? count($item) : strlen($item);
            },
        ]),
        // other compilation targets...
    ]
);

return $rulerz;
