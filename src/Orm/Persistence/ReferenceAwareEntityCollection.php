<?php

declare(strict_types = 1);

namespace App\Orm\Persistence;

use App\Orm\Entity\AbstractEntity;
use ArrayAccess;
use Countable;
use Iterator;
use IteratorAggregate;
use ArrayIterator;
use JsonSerializable;

use function array_map;
use function count;

/**
 * Collection for storing AbstractEntity instances.
 * Holds reference on wrapping LayoutObject instance to establish connection between Entities and outer LayoutObject.
 *
 * MUST be used only with instances of AbstractEntity.
 *
 * @see     \App\Orm\Entity\AbstractEntity
 *
 * @package App\Orm\Persistence
 */
final class ReferenceAwareEntityCollection implements IteratorAggregate, ArrayAccess, JsonSerializable, Countable
{
    /**
     * @var array<\App\Orm\Entity\AbstractEntity> List of inner items.
     */
    private array $container;

    /**
     * @var \App\Orm\Persistence\LayoutObject Reference on wrapping LayoutObject instance.
     */
    private LayoutObject $reference;

    /**
     * ReferenceAwareEntityCollection constructor.
     *
     * Initialize root attribute of inner AbstractEntities.
     *
     * @param array<\App\Orm\Entity\AbstractEntity> $entities
     */
    public function __construct(array $entities = [])
    {
        $this->container = $entities;
        array_map(function (AbstractEntity $entity) : void {
            $entity->setRoot($this);
        }, $this->container);
    }

    public function getIterator() : Iterator
    {
        return new ArrayIterator($this->container);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * @param mixed                          $offset
     * @param \App\Orm\Entity\AbstractEntity $value
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }

        $value->setRoot($this);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * @return \App\Orm\Persistence\LayoutObject
     */
    public function getReference() : LayoutObject
    {
        return $this->reference;
    }

    /**
     * @param \App\Orm\Persistence\LayoutObject $reference
     */
    public function setReference(LayoutObject $reference) : void
    {
        $this->reference = $reference;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->container;
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->container);
    }
}