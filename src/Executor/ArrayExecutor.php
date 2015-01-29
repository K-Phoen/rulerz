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

    public function filter(Model $rule, $target, array $parameters = [])
    {
        $newParameters = $this->prepareParameters($parameters);

        return array_filter($target, function($row) use ($rule, $newParameters) {
            return $this->filterRow($rule, $row, $newParameters);
        });
    }

    public function supports($target)
    {
        return is_array($target);
    }

    private function filterRow(Model $rule, $row, array $parameters)
    {
        $this->asserter->setContext($this->createContext($row, $parameters));

        return $this->asserter->visit($rule);
    }

    private function createContext($row, array $parameters)
    {
        return is_array($row)
            ? new ArrayContext(array_merge($row, $parameters))
            : new ObjectContext($row, $parameters);
    }

    private function prepareParameters(array $parameters)
    {
        $newParameters = [];

        foreach ($parameters as $name => $value) {
            $newParameters[':'.$name] = $value;
        }

        return $newParameters;
    }
}
