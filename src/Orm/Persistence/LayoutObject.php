<?php

namespace App\Orm\Persistence;

use JsonSerializable;

class LayoutObject implements JsonSerializable
{
    /**
     * @var \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    protected ReferenceAwareEntityCollection $tree;

    /**
     * @var array
     */
    protected array $hashes = [];

    public function __construct(ReferenceAwareEntityCollection $tree, array $hashes)
    {
        $this->tree = $tree;
        $this->tree->setReference($this);
        $this->hashes = $hashes;
    }

    public function getHashes() : array
    {
        return $this->hashes;
    }

    /**
     * @return \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    public function getTree() : ReferenceAwareEntityCollection
    {
        return $this->tree;
    }

    public function jsonSerialize()
    {
        return $this->getTree();
    }
}