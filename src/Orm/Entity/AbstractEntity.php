<?php

namespace App\Orm\Entity;

use App\Orm\Persistence\ReferenceAwareEntityCollection;
use JsonSerializable;

abstract class AbstractEntity implements JsonSerializable
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @var \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    protected ReferenceAwareEntityCollection $root;

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type) : void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getHash() : ?string
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash(string $hash) : void
    {
        $this->hash = $hash;
    }

    /**
     * @return array
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties) : void
    {
        $this->properties = $properties;
    }

    /**
     * @return \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    public function getRoot() : ReferenceAwareEntityCollection
    {
        return $this->root;
    }

    /**
     * @param \App\Orm\Persistence\ReferenceAwareEntityCollection $root
     */
    public function setRoot(ReferenceAwareEntityCollection $root) : void
    {
        $this->root = $root;
    }

    public function jsonSerialize()
    {
        return [
            'type'  => $this->getType(),
            'hash'  => $this->getHash(),
            'props' => $this->getProperties(),
        ];
    }
}