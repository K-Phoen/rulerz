<?php

declare(strict_types=1);

namespace RulerZ\Spec;

class AndX extends Composite
{
    public function __construct(array $specifications = [])
    {
        parent::__construct('AND', $specifications);
    }
}
