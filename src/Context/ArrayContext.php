<?php

namespace RulerZ\Context;

class ArrayContext implements \ArrayAccess
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor.
     *
     * @param array $data The array to extract data from.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($id)
    {
        if (!array_key_exists($id, $this->data)) {
            throw new \RuntimeException(sprintf('Key %s does not exist.', $id));
        }

        return $this->data[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($id)
    {
        return array_key_exists($id, $this->data);
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
