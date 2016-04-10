<?php

namespace RulerZ\Stub\Executor;

use RulerZ\Executor\DoctrineORM\FilterTrait;

class DoctrineORMExecutorStub
{
    public static $executeReturn;
    public $detectedJoins = [];

    use FilterTrait;

    public function execute($target, array $operators, array $parameters)
    {
        return self::$executeReturn;
    }
}