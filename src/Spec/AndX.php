<?php

namespace RulerZ\Spec;

class AndX extends Composite
{
    public function __construct(array $specifications = [])
    {
        parent::__construct('AND', $specifications);
    }
}
