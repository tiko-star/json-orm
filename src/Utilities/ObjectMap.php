<?php

declare(strict_types = 1);

namespace App\Utilities;

use Countable;
use Exception;
use IteratorAggregate;
use Traversable;
use ArrayIterator;

use function count;

/**
 * An ObjectMap is a sequential collection of key-value pairs, almost identical to an array used in a similar context.
 * Keys must be string type, but must be unique.
 * Values must be object type.
 * Values are replaced if added to the map using the same key.
 *
 * @package App\Utilities
 */
class ObjectMap implements Countable, IteratorAggregate
{
    /**
     * @var array Container to store entries.
     */
    protected array $entries = [];

    /**
     * ObjectMap constructor.
     * Create a new instance, using either a iterable object or an array for the initial values.
     *
     * @param iterable $entries A iterable object or an array to use for the initial values.
     */
    public function __construct(iterable $entries = [])
    {
        foreach ($entries as $key => $entry) {
            $this->set((string) $key, $entry);
        }
    }

    /**
     * Associate a key with a value, overwriting a previous association if one exists.
     *
     * @param string $key   The key to associate the value with.
     * @param object $entry The value to be associated with the key.
     *
     * @return $this
     */
    public function set(string $key, object $entry) : ObjectMap
    {
        $this->entries[$key] = $entry;

        return $this;
    }

    /**
     * Return the value for a given key.
     *
     * @param string $key The key to look up.
     *
     * @return object|null
     */
    public function get(string $key) : ?object
    {
        return $this->entries[$key] ?? null;
    }

    /**
     * Determine whether the map contains a given key.
     *
     * @param string $key The key to look for.
     *
     * @return bool
     */
    public function has(string $key) : bool
    {
        return isset($this->entries[$key]);
    }

    /**
     * Remove and return a value by key.
     *
     * @param string $key The key to remove.
     *
     * @return object|null
     */
    public function delete(string $key) : ?object
    {
        if (!$this->has($key)) {
            return null;
        }

        $entry = $this->get($key);
        unset($this->entries[$key]);

        return $entry;
    }

    /**
     * Remove all values from the map.
     *
     * @return \App\Utilities\ObjectMap
     */
    public function clear() : ObjectMap
    {
        $this->entries = [];

        return $this;
    }

    /**
     * Count elements of an object
     *
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *
     * The return value is cast to an integer.
     */
    public function count() : int
    {
        return count($this->entries);
    }

    /**
     * Retrieve an external iterator
     *
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *
     * @throws Exception on failure.
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->entries);
    }

    /**
     * Return an array containing all the keys of the map, in the same order.
     *
     * @return array An array containing all the keys of the map.
     */
    public function keys() : array
    {
        return array_keys($this->entries);
    }

    /**
     * Return an array containing all the values of the map, in the same order.
     *
     * @return array An array containing all the values of the map.
     */
    public function values() : array
    {
        return array_values($this->entries);
    }
}
