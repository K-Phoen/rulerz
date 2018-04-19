<?php

declare(strict_types=1);

namespace RulerZ\Spec;

/**
 * Base class for specifications. Only provides a few usefull shortcuts.
 */
abstract class AbstractSpecification implements Specification
{
    /**
     * Create a conjunction with the current specification and another one.
     *
     * @param Specification $spec The other specification.
     */
    public function andX(Specification $spec): AndX
    {
        return new AndX([$this, $spec]);
    }

    /**
     * Create a disjunction with the current specification and another one.
     *
     * @param Specification $spec The other specification.
     */
    public function orX(Specification $spec): OrX
    {
        return new OrX([$this, $spec]);
    }

    /**
     * Negate the current specification.
     *
     * @return Not
     */
    public function not(): Not
    {
        return new Not($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return [];
    }
}
