<?php

declare(strict_types = 1);

namespace App\Orm\Persistence;

use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;

/**
 * Object oriented representation and implementation of the JSON based layout data.
 *
 * @package App\Orm\Persistence
 */
class LayoutObject implements JsonSerializable
{
    /**
     * @var \App\Orm\Persistence\ReferenceAwareEntityCollection Tree of AbstractEntity instances inside.
     */
    protected ReferenceAwareEntityCollection $tree;

    /**
     * @var array<string> List of the entity hashes inside.
     */
    protected array $hashes = [];

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection Reference on list of contents of current AbstractEntity
     *      instances inside.
     */
    protected ArrayCollection $contents;

    public function getHashes() : array
    {
        return $this->hashes;
    }

    public function setHashes(array $hashes) : void
    {
        $this->hashes = $hashes;
    }

    /**
     * @return \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    public function getTree() : ReferenceAwareEntityCollection
    {
        return $this->tree;
    }

    /**
     * @param \App\Orm\Persistence\ReferenceAwareEntityCollection $tree
     */
    public function setTree(ReferenceAwareEntityCollection $tree) : void
    {
        $this->tree = $tree;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getContents() : ArrayCollection
    {
        return $this->contents;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $contents
     */
    public function setContents(ArrayCollection $contents) : void
    {
        $this->contents = $contents;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    public function jsonSerialize() : ReferenceAwareEntityCollection
    {
        return $this->getTree();
    }
}