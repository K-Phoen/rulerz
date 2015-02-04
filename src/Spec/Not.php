<?php

namespace RulerZ\Spec;

/**
 * Negates a specification.
 */
class Not implements Specification
{
    /**
     * @var Specification
     */
    private $specification;

    /**
     * Constructor.
     *
     * @param Specification $specification The specification to negate.
     */
    public function __construct(Specification $specification)
    {
        $this->specification = $specification;
    }

    /**
     * {@inheritDoc}
     */
    public function getRule()
    {
        return sprintf('NOT (%s)', $this->specification->getRule());
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()
    {
        return $this->specification->getParameters();
    }
}
