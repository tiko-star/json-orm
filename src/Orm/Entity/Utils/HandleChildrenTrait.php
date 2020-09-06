<?php

namespace App\Orm\Entity\Utils;

use App\Orm\Persistence\ReferenceAwareEntityCollection;

trait HandleChildrenTrait
{
    /**
     * @var \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    protected  ReferenceAwareEntityCollection $children;

    /**
     * @return \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    public function getChildren() : ReferenceAwareEntityCollection
    {
        return $this->children;
    }

    /**
     * @param \App\Orm\Persistence\ReferenceAwareEntityCollection $children
     */
    public function setChildren(ReferenceAwareEntityCollection $children) : void
    {
        $this->children = $children;
    }
}