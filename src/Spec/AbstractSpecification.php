<?php

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
     *
     * @return Specification
     */
    public function andX(Specification $spec)
    {
        return new AndX([$this, $spec]);
    }

    /**
     * Create a disjunction with the current specification and another one.
     *
     * @param Specification $spec The other specification.
     *
     * @return Specification
     */
    public function orX(Specification $spec)
    {
        return new OrX([$this, $spec]);
    }

    /**
     * Negate the current specification.
     *
     * @return Specification
     */
    public function not()
    {
        return new Not($this);
    }
}
