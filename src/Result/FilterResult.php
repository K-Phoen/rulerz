<?php

namespace RulerZ\Result;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Result of a RulerZ::filter() operation.
 *
 * @see \RulerZ\RulerZ::filter()
 */
class FilterResult implements IteratorAggregate
{
    /**
     * @var Traversable
     */
    private $traversable;

    /**
     * Creates a FilterResult instance from an array.
     *
     * @param array $results
     *
     * @return FilterResult
     */
    public static function fromArray(array $results)
    {
        return new static(new ArrayIterator($results));
    }

    /**
     * Creates a FilterResult instance from a generator.
     * Here is an example of valid usage:
     * FilterResult::fromGenerator(function() {
     *     yield 1;
     *     yield 2;
     *     yield 3;
     * });
     *
     * @param callable $generatorCallable A callable, which will return a generator once called.
     *
     * @return FilterResult
     */
    public static function fromGenerator(callable $generatorCallable)
    {
        return new static($generatorCallable());
    }

    /**
     * Creates a FilterResult instance from a traversable object.
     *
     * @param Traversable $traversable
     *
     * @return FilterResult
     */
    public static function fromTraversable(Traversable $traversable)
    {
        return new static($traversable);
    }

    /**
     * @param callable $traversable A traversable object, representing the results.
     */
    private function __construct(Traversable $traversable)
    {
        $this->traversable = $traversable;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->traversable;
    }
}
