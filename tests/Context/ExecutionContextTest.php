<?php

namespace RulerZ\Tests\Context;

use RulerZ\Context\ExecutionContext;

class ExecutionContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExecutionContext
     */
    private $context;

    public function setUp()
    {
        $this->context = new ExecutionContext([
            'foo' => 'bar',
        ]);
    }

    public function testDataCanBeRetrieved()
    {
        $this->assertSame('bar', $this->context['foo']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Identifier "not found" does not exist
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
     * @expectedException \LogicException
     * @expectedExceptionMessage The execution context is read-only.
     */
    public function testTheContextIsReadOnly()
    {
        $this->context['bar'] = 'baz';
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The execution context is read-only.
     */
    public function testKeysCanNotBeDeleted()
    {
        unset($this->context['bar']);
    }
}
