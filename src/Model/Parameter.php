<?php

namespace RulerZ\Model;

use Hoa\Visitor;

class Parameter implements Visitor\Element
{
    /**
     * The parameter's name.
     *
     * @var string
     */
    private $name;

    /**
     * Constructor.
     *
     * @param string $name The name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Accept a visitor.
     *
     * @param   \Hoa\Visitor\Visit  $visitor    Visitor.
     * @param   mixed               &$handle    Handle (reference).
     * @param   mixed               $eldnah     Handle (no reference).
     * @return  mixed
     */
    public function accept(Visitor\Visit $visitor, &$handle = null, $eldnah = null)
    {
        return $visitor->visit($this, $handle, $eldnah);
    }
}
