<?php

namespace RulerZ\Exception;

class OperatorNotFoundException extends \RuntimeException
{
    private $operator;

    public function __construct($operator, $msg, \Exception $previous = null)
    {
        parent::__construct($msg, 0, $previous);

        $this->operator = $operator;
    }

    public function getOperator()
    {
        return $this->operator;
    }
}
