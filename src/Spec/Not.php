<?php

declare(strict_types=1);

namespace RulerZ\Spec;

/**
 * Negates a specification.
 */
class Not extends AbstractSpecification
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
     * {@inheritdoc}
     */
    public function getRule(): string
    {
        return sprintf('NOT (%s)', $this->specification->getRule());
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return $this->specification->getParameters();
    }
}
