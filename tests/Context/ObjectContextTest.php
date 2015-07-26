<?php

namespace RulerZ\Tests\Context;

use RulerZ\Context\ObjectContext;

class ObjectContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectContext
     */
    private $context;

    public function setUp()
    {
        $object = (object) [
            'foo' => 'bar',
        ];

        $this->context = new ObjectContext($object);
    }

    public function testDataCanBeRetrieved()
    {
        $this->assertSame('bar', $this->context['foo']);
    }

    /**
     * @expectedException \Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException
     */
    public function testAccessingNonExistentKeysFails()
    {
        $this->context['not found'];
    }

    public function testExistenceCanBeTested()
    {
        $this->assertTrue(isset($this->context['foo']));
        $this->assertFalse(isset($this->context['not found']));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Context is read-only.
     */
    public function testTheContextIsReadOnly()
    {
        $this->context['bar'] = 'baz';
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Context is read-only.
     */
    public function testKeysCanNotBeDeleted()
    {
        unset($this->context['bar']);
    }
}
