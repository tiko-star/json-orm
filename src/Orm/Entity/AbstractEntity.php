<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

use App\Doctrine\Entity\Content;
use App\Orm\Persistence\ReferenceAwareEntityCollection;
use JsonSerializable;

/**
 * Base class for all kind of Entities.
 * Contains all general methods.
 *
 * @package App\Orm\Entity
 */
abstract class AbstractEntity implements JsonSerializable
{
    /**
     * @var string The type of the current entity.
     */
    protected string $type;

    /**
     * @var string|null The unique hash of the current entity.
     */
    protected ?string $hash;

    /**
     * @var array Individual properties of the current entity.
     */
    protected array $properties = [];

    /**
     * @var \App\Orm\Persistence\ReferenceAwareEntityCollection Reference on the instance of the wrapping object.
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
     * @return string|null
     */
    public function getHash() : ?string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
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

    /**
     * Apply content data to the current entity.
     * This method will only be invoked during JSON serialization process.
     */
    public function applyContent() : void
    {
        $contents = $this->getRoot()->getReference()->getContents();

        /** @var Content|false $content */
        $content = $contents
            ->filter(fn(Content $content) => $content->getHash() === $this->getHash())
            ->first();

        if ($content) {
            $this->initializeContent($content);
        }
    }

    /**
     * Initialize content data for current entity.
     *
     * @param \App\Doctrine\Entity\Content $content
     */
    protected abstract function initializeContent(Content $content) : void;

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        $this->applyContent();

        return [
            'type'  => $this->getType(),
            'hash'  => $this->getHash(),
            'props' => $this->getProperties(),
        ];
    }
}