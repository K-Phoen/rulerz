<?php

namespace RulerZ\Result;

use ArrayIterator;

/**
 * Set of utility methods to create traversable objects from various inputs.
 */
class IteratorTools
{
    /**
     * Creates an iterator from an array.
     *
     * Usage:
     *
     * ```
     * IteratorTools::fromArray([1, 2, 3]);
     * ```
     *
     * @param array $results
     *
     * @return \ArrayIterator
     */
    public static function fromArray(array $results)
    {
        return new ArrayIterator($results);
    }

    /**
     * Creates an iterator from a generator.
     *
     * Example of valid usage:
     *
     * ```
     * IteratorTools::fromGenerator(function() {
     *     yield 1;
     *     yield 2;
     *     yield 3;
     * });
     * ```
     *
     * @param callable $generatorCallable A callable, which will return a generator once called.
     *
     * @return \Iterator
     */
    public static function fromGenerator(callable $generatorCallable)
    {
        return $generatorCallable();
    }
}
