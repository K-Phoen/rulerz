<?php

namespace RulerZ\Stub\Executor;

use RulerZ\Executor\ArrayTarget\FilterTrait;

class ArrayExecutorStub
{
    public static $executeReturn;

    use FilterTrait;

    public function execute($target, array $operators, array $parameters)
    {
        return self::$executeReturn;
    }
}