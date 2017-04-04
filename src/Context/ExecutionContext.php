<?php

namespace RulerZ\Context;

class ExecutionContext implements \ArrayAccess
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param array $data The context data.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get($key, $default = null)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Get a data.
     *
     * @param string $key Key.
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new \RuntimeException(sprintf('Identifier "%s" does not exist in the context.', $key));
        }

        return $this->data[$key];
    }

    /**
     * Check if a data exists.
     *
     * @param string $key Key.
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Set a data.
     *
     * @param string $key   Key.
     * @param mixed  $value Value.
     */
    public function offsetSet($key, $value)
    {
        throw new \LogicException('The execution context is read-only.');
    }

    /**
     * Unset a data.
     *
     * @param string $key Key.
     */
    public function offsetUnset($key)
    {
        throw new \LogicException('The execution context is read-only.');
    }
}
