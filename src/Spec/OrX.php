<?php

namespace RulerZ\Spec;

class OrX extends Composite
{
    public function __construct(array $specifications = [])
    {
        parent::__construct('OR', $specifications);
    }
}
