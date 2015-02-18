<?php

namespace SampleSpecs;

use RulerZ\Spec\Specification;

class MinScore implements Specification
{
    private $min_score;

    public function __construct($min_score)
    {
        $this->min_score = $min_score;
    }

    public function getRule()
    {
        return 'points > :min_score';
    }

    public function getParameters()
    {
        return [
            'min_score' => $this->min_score,
        ];
    }
}
