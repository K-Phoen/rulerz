<?php

namespace RulerZ\Target\Operators;

class RuntimeOperator
{
    /**
     * @var string
     */
    private $callable;

    /**
     * @var array
     */
    private $arguments;

    public function __construct($callable, $arguments)
    {
        $this->callable = $callable;
        $this->arguments = $arguments;
    }

    public function format($shouldBreakString)
    {
        $formattedArguments = join(',', array_map(function ($argument) {
            if ('$' === $argument[0]) {
                return $argument;
            } else {
                return sprintf('"%s"', $argument);
            }
        }, $this->arguments));

        if (true === $shouldBreakString) {
            return sprintf(
                '".call_user_func_array(%s, [%s])."',
                $this->callable,
                $formattedArguments
            );
        } else {
            return sprintf(
                'call_user_func_array(%s, [%s])',
                $this->callable,
                $formattedArguments
            );
        }
    }
}
