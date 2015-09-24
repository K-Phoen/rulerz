<?php

namespace SampleSpecs;

use RulerZ\Spec\AbstractSpecification;

class FemalePlayer extends AbstractSpecification
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
