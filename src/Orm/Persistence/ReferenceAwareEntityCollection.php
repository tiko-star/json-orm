<?php

declare(strict_types = 1);

namespace App\Orm\Persistence;

use App\Orm\Entity\AbstractEntity;
use ArrayAccess;
use Iterator;
use IteratorAggregate;
use ArrayIterator;
use JsonSerializable;

final class ReferenceAwareEntityCollection implements IteratorAggregate, ArrayAccess, JsonSerializable
{
    /**
     * @var array<\App\Orm\Entity\AbstractEntity>
     */
    private array $container;

    /**
     * @var \App\Orm\Persistence\LayoutObject Reference on wrapping LayoutObject instance.
     */
    private LayoutObject $reference;

    /**
     * ReferenceAwareEntityCollection constructor.
     *
     * @param array<\App\Orm\Entity\AbstractEntity> $entities
     */
    public function __construct(array $entities = [])
    {
        $this->container = $entities;
        array_map(fn(AbstractEntity $entity) => $entity->setRoot($this), $this->container);
    }

    public function getIterator() : Iterator
    {
        return new ArrayIterator($this->container);
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

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

    public function jsonSerialize() : array
    {
        return $this->container;
    }
}