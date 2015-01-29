<?php

namespace Executor;

use Hoa\Ruler\Context as ArrayContext;
use Hoa\Ruler\Model;
use Hoa\Ruler\Visitor\Asserter;

use Context\ObjectContext;

class ArrayExecutor implements Executor
{
    private $asserter;

    public function __construct()
    {
        $this->asserter = new Asserter();
    }

    public function filter(Model $rule, $target)
    {
        return array_filter($target, function($row) use ($rule) {
            return $this->filterRow($rule, $row);
        });
    }

    public function supports($target)
    {
        return is_array($target);
    }

    private function filterRow(Model $rule, $row)
    {
        $this->asserter->setContext($this->createContext($row));

        return $this->asserter->visit($rule);
    }

    private function createContext($row)
    {
        return is_array($row) ? new ArrayContext($row) : new ObjectContext($row);
    }
}
