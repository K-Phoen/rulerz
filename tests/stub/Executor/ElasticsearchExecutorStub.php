<?php

namespace RulerZ\Stub\Executor;

use RulerZ\Executor\Elasticsearch\ElasticsearchFilterTrait;

class ElasticsearchExecutorStub
{
    public static $executeReturn;

    use ElasticsearchFilterTrait;

    public function execute($target, array $operators, array $parameters)
    {
        return self::$executeReturn;
    }
}
