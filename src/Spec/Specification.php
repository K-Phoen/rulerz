<?php

declare(strict_types=1);

namespace RulerZ\Spec;

/**
 * Represents a specification as in the Specification pattern.
 */
interface Specification
{
    /**
     * The rule representing the specification.
     */
    public function getRule(): string;

    /**
     * The parameters used in the specification.
     */
    public function getParameters(): array;
}
