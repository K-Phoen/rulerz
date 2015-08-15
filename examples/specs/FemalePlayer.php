<?php

namespace SampleSpecs;

use RulerZ\Spec\Specification;

class FemalePlayer implements Specification
{
    public function getRule()
    {
        return 'gender = "F"';
    }

    public function getParameters()
    {
        return [];
    }
}
