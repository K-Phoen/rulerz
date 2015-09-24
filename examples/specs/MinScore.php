<?php

namespace SampleSpecs;

use RulerZ\Spec\AbstractSpecification;

class MinScore extends AbstractSpecification
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
