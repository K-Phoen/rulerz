<?php

declare(strict_types=1);

namespace SampleSpecs;

use RulerZ\Spec\AbstractSpecification;

class FemalePlayer extends AbstractSpecification
{
    public function getRule(): string
    {
        return 'gender = "F"';
    }
}
