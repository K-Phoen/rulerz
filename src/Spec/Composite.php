<?php

namespace RulerZ\Spec;

class Composite implements Specification
{
    /**
     * @var string $operator
     */
    private $operator;

    /**
     * @var array
     */
    private $specifications = [];

    /**
     * Builds a composite specification.
     *
     * @param string $operator       The operator used to join the specifications.
     * @param array  $specifications A list of initial specifications.
     */
    public function __construct($operator, array $specifications = [])
    {
        $this->operator = $operator;

        foreach ($specifications as $specification) {
            $this->addSpecification($specification);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRule()
    {
        return implode(sprintf(' %s ', $this->operator), array_map(function (Specification $specification) {
            return $specification->getRule();
        }, $this->specifications));
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()
    {
        return call_user_func_array('array_merge', array_map(function (Specification $specification) {
            return $specification->getParameters();
        }, $this->specifications));
    }

    /**
     * Adds a new specification.
     *
     * @param Specification $specification
     */
    public function addSpecification(Specification $specification)
    {
        $this->specifications[] = $specification;
    }
}
