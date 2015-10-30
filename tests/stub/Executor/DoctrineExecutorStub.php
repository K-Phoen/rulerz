<?php

namespace RulerZ\Stub\Executor;

use RulerZ\Executor\DoctrineQueryBuilder\FilterTrait;

class DoctrineExecutorStub
{
    public static $executeReturn;

    use FilterTrait;

    public function execute($target, array $operators, array $parameters)
    {
        return self::$executeReturn;
    }
}