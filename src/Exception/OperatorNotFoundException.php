<?php

declare(strict_types=1);

namespace RulerZ\Exception;

class OperatorNotFoundException extends \RuntimeException
{
    private $operator;

    public function __construct(string $operator, $msg, \Exception $previous = null)
    {
        parent::__construct($msg, 0, $previous);

        $this->operator = $operator;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }
}
