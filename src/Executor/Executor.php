<?php

namespace Executor;

use Hoa\Ruler\Model;

interface Executor
{
    function filter(Model $rule, $target, array $parameters = []);

    function supports($target);
}
