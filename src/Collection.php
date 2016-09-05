<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use OutOfRangeException;
use Traversable;

class Collection implements
    ArrayAccess,
    Countable,
    IteratorAggregate
{
    /**
     * @param array
     */
    protected $items;

    /**
     * @param array|Traversable $items
     * @throws InvalidArgumentException
     */
    public function __construct($items)
    {
        if ($items instanceof Traversable) {
            $items = iterator_to_array($items);
        }

        if (! is_array($items)) {
            throw new InvalidArgumentException('Collections require arrays or Traversable objects');
        }

        $this->items = $items;
    }

    /**
     * Factory method
     *
     * @param array|Traversable
     * @return static
     */
    public static function create($items) : self
    {
        return new static($items);
    }

    /**
     * Cast collection to an array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->items;
    }

    /**
     * Apply a callback to each item in the collection.
     *
     * @param callable $callback
     * @return self
     */
    public function each(callable $callback) : self
    {
        foreach ($this->items as $item) {
            $callback($item);
        }
        return $this;
    }

    /**
     * Reduce the collection to a single value.
     *
     * @param callable $callback
     * @param mixed $initial Initial value.
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        $accumulator = $initial;

        foreach ($this->items as $item) {
            $accumulator = $callback($accumulator, $item);
        }

        return $accumulator;
    }

    /**
     * Filter the collection using a callback.
     *
     * Filter callback should return true for values to keep.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback) : self
    {
        return $this->reduce(function ($filtered, $item) use ($callback) {
            if ($callback($item)) {
                $filtered[] = $item;
            }
            return $filtered;
        }, new static([]));
    }

    /**
     * Filter items through a filter chain.
     *
     * Passes each item through a collection of filters, keeping only
     * those items that pass all filters.
     *
     * @param array $filters
     * @return static
     */
    public function filterChain(array $filters) : self
    {
        $filters = static::create($filters);
        return $this->filter(function ($item) use ($filters) {
            return $filters
                ->reduce(function ($keep, callable $filter) use ($item) {
                    if (! $keep) {
                        return $keep;
                    }

                    return $filter($item);
                }, true);
        });
    }

    /**
     * Filter the collection using a callback; reject any items matching the callback.
     *
     * Filter callback should return true for values to reject.
     *
     * @param callable $callback
     * @return static
     */
    public function reject(callable $callback) : self
    {
        return $this->reduce(function ($filtered, $item) use ($callback) {
            if (! $callback($item)) {
                $filtered[] = $item;
            }
            return $filtered;
        }, new static([]));
    }

    /**
     * Transform each value in the collection.
     *
     * Callback should return the new value to use.
     *
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback) : self
    {
        return $this->reduce(function ($results, $item) use ($callback) {
            $results[] = $callback($item);
            return $results;
        }, new static([]));
    }

    /**
     * Return a slice of the total set of items.
     *
     * @param int $length
     * @param int $offset
     * @return static
     */
    public function slice(int $length, int $offset = 0) : self
    {
        return new static(array_slice($this->items, $offset, $length, true));
    }

    /**
     * Return a collection of items sorted based on the callback provided.
     *
     * @param callable $sorter
     * @return static
     */
    public function sort(callable $sorter) : self
    {
        $items = $this->items;
        usort($items, $sorter);
        return static::create($items);
    }

    /**
     * Append this collection with another.
     *
     * @param Collection $items
     * @return static
     */
    public function append(Collection $items) : self
    {
        return Collection::create(array_merge($this->items, $items->toArray()));
    }

    /**
     * ArrayAccess: isset()
     *
     * @param string|int $offset
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * ArrayAccess: retrieve by key
     *
     * @param string|int $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (! $this->offsetExists($offset)) {
            throw new OutOfRangeException(sprintf(
                'Offset %s does not exist in the collection',
                $offset
            ));
        }

        return $this->items[$offset];
    }

    /**
     * ArrayAccess: set by key
     *
     * If $offset is null, pushes the item onto the stack.
     *
     * @param string|int $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->items[] = $value;
            return;
        }

        $this->items[$offset] = $value;
    }

    /**
     * ArrayAccess: unset()
     *
     * @param string|int $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->items[$offset]);
        }
    }

    /**
     * Countable: number of items in the collection.
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->items);
    }

    /**
     * Is the collection empty?
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        return 0 === $this->count();
    }

    /**
     * Traversable: Iterate the collection.
     *
     * @return ArrayIterator
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
