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
     * @param mixed $object The object to extract data from.
     */
    public function __construct($object)
    {
        $this->object = $object;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Returns the object of the context.
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($id)
    {
        $value = $this->accessor->getValue($this->object, $id);

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        return new static($value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($id)
    {
        return $this->accessor->isReadable($this->object, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($id, $value)
    {
        throw new \RuntimeException('Context is read-only.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($id)
    {
        throw new \RuntimeException('Context is read-only.');
    }
}
