<?php

declare(strict_types=1);

namespace RulerZ\Result;

use ArrayIterator;

/**
 * Set of utility methods to create traversable objects from various inputs.
 */
class IteratorTools
{
    /**
     * Ensures that the given items are \Traversable.
     *
     * Usage:
     *
     * ```
     * IteratorTools::ensureTraversable([1, 2, 3]);
     * IteratorTools::ensureTraversable(new \ArrayIterator([1, 2, 3]));
     * ```
     *
     * @param mixed $items
     *
     * @throws \InvalidArgumentException
     */
    public static function ensureTraversable($items): \Traversable
    {
        if ($items instanceof \Traversable) {
            return $items;
        }

        if (is_array($items)) {
            return self::fromArray($items);
        }

        throw new \InvalidArgumentException('Un-handled argument of type: '.get_class($items));
    }

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
     */
    public static function fromArray(array $results): \Traversable
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
     */
    public static function fromGenerator(callable $generatorCallable): \Traversable
    {
        return $generatorCallable();
    }
}
