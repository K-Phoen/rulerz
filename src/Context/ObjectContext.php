<?php

namespace Context;

use Hoa\Ruler\Context as BaseContext;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ObjectContext extends BaseContext
{
    private $object;
    private $accessor;

    /**
     * Constructor.
     *
     * @access  public
     * @param   array  $data    Initial data.
     * @return  void
     */
    public function __construct ( $object ) {

        $this->object = $object;
        $this->accessor = PropertyAccess::createPropertyAccessor();

        return;
    }

    /**
     * Set a data.
     *
     * @access  public
     * @param   string  $id       ID.
     * @param   mixed   $value    Value.
     * @return  void
     */
    public function offsetSet ( $id, $value ) {

        $this->accessor->setValue($this->object, $id, $value);

        return;
    }

    /**
     * Get a data.
     *
     * @access  public
     * @param   string  $id    ID.
     * @return  mixed
     * @throw   \Hoa\Ruler\Exception
     */
    public function offsetGet ( $id ) {

        $value = $this->accessor->getValue($this->object, $id);

        if($value instanceof DynamicCallable)
            return $value($this);

        if(true === is_callable($value))
            $value = $this->_data[$id] = $value($this);

        return $value;
    }

    /**
     * Check if a data exists.
     *
     * @access  public
     * @return  bool
     */
    public function offsetExists ( $id ) {

        return $this->accessor->isReadable($this->object, $id);
    }

    /**
     * Unset a data.
     *
     * @access  public
     * @param   string  $id    ID.
     * @return  void
     */
    public function offsetUnset ( $id ) {
    }
}
