<?php

namespace App\Orm\Entity\Contracts;

use App\Orm\Persistence\ReferenceAwareEntityCollection;

interface ContainsChildrenInterface
{
    /**
     * @return \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    public function getChildren() : ReferenceAwareEntityCollection;

    /**
     * @param \App\Orm\Persistence\ReferenceAwareEntityCollection $children
     */
    public function setChildren(ReferenceAwareEntityCollection $children) : void;
}