<?php

namespace RulerZ\Stub\Executor;

use RulerZ\Executor\DoctrineDBAL\FilterTrait;

class DoctrineDBALExecutorStub
{
    public static $executeReturn;

    use FilterTrait;

    public function execute($target, array $operators, array $parameters)
    {
        return self::$executeReturn;
    }
}
