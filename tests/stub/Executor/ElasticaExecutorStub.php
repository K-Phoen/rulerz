<?php

namespace RulerZ\Stub\Executor;

use RulerZ\Executor\Elasticsearch\ElasticaFilterTrait;

class ElasticaExecutorStub
{
    public static $executeReturn;

    use ElasticaFilterTrait;

    public function execute($target, array $operators, array $parameters)
    {
        return self::$executeReturn;
    }
}