<?php

namespace RulerZ\Context;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ObjectContext implements \ArrayAccess
{
    /**
     * @var mixed
     */
    private $object;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $accessor;

    /**
     * Constructor.
     *
     * @param mixed $object The object to extract data from.
     */
    public function __construct($object)
    {
        $this->object   = $object;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($id)
    {
        return $this->accessor->getValue($this->object, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($id)
    {
        return $this->accessor->isReadable($this->object, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($id, $value)
    {
        throw new \RuntimeException('Context is read-only.');
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($id)
    {
        throw new \RuntimeException('Context is read-only.');
    }
}
