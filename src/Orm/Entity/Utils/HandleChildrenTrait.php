<?php

declare(strict_types = 1);

namespace App\Orm\Entity\Utils;

use App\Orm\Persistence\ReferenceAwareEntityCollection;

/**
 * Utility for enabling children support of AbstractEntities.
 * Defines methods declared in ContainsChildrenInterface.
 *
 * @see     \App\Orm\Entity\Contracts\ContainsChildrenInterface
 *
 * @package App\Orm\Entity\Utils
 */
trait HandleChildrenTrait
{
    /**
     * @var \App\Orm\Persistence\ReferenceAwareEntityCollection Reference on instance of the children property.
     */
    protected ReferenceAwareEntityCollection $children;

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