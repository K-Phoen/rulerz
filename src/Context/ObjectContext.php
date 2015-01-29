<?php

namespace Context;

use Hoa\Ruler\Context as BaseContext;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ObjectContext extends BaseContext
{
    /**
     * @var mixed
     */
    private $object;

    /**
     * @var PropertyAccess
     */
    private $accessor;

    /**
     * Constructor.
     *
     * @param mixed $object The object to extract data from.
     * @param array $data   Additionnal data.
     */
    public function __construct($object, array $data = [])
    {
        $this->object   = $object;
        $this->_data    = $data;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Set a data.
     *
     * @param   string  $id       ID.
     * @param   mixed   $value    Value.
     */
    public function offsetSet($id, $value)
    {
        if ($this->accessor->isReadable($this->object, $id)) {
            $this->accessor->setValue($this->object, $id, $value);
            return;
        }

        parent::offsetSet($id, $value);
    }

    /**
     * Get a data.
     *
     * @param   string  $id    ID.
     * @return  mixed
     * @throw   \Hoa\Ruler\Exception
     */
    public function offsetGet($id)
    {
        if (array_key_exists($id, $this->_data)) {
            return parent::offsetGet($id);
        }

        return $this->accessor->getValue($this->object, $id);
    }

    /**
     * Check if a data exists.
     *
     * @return  bool
     */
    public function offsetExists($id)
    {
        return parent::offsetExists($id) || $this->accessor->isReadable($this->object, $id);
    }
}
