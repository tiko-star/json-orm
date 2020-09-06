<?php

declare(strict_types = 1);

namespace App\Orm\Entity\Contracts;

use App\Orm\Persistence\ReferenceAwareEntityCollection;

/**
 * Interface for declaring methods for children support of AbstractEntities.
 *
 * @package App\Orm\Entity\Contracts
 */
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