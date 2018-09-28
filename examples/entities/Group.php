<?php

namespace Entity;

class Group
{
    public $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
