<?php

namespace RulerZ\Spec;

/**
 * Represents a specification as in the Specification pattern.
 */
interface Specification
{
    /**
     * The rule representing the specification.
     *
     * @return string
     */
    public function getRule();

    /**
     * The parameters used in the specification.
     *
     * @return array
     */
    public function getParameters();
}
