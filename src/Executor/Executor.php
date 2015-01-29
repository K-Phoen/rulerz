<?php

namespace Executor;

use Hoa\Ruler\Model;

interface Executor
{
    function filter(Model $rule, $target);

    function supports($target);
}
