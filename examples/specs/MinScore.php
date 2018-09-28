<?php

declare(strict_types=1);

namespace SampleSpecs;

use RulerZ\Spec\AbstractSpecification;

class MinScore extends AbstractSpecification
{
    private $minScore;

    public function __construct(int $minScore)
    {
        $this->minScore = $minScore;
    }

    public function getRule(): string
    {
        return 'points > :min_score';
    }

    public function getParameters(): array
    {
        return [
            'min_score' => $this->minScore,
        ];
    }
}
