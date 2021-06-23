<?php

declare(strict_types = 1);

namespace App\Orm\Entity\Decorators;

use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use App\Orm\Persistence\ReferenceAwareEntityCollection;

/**
 * Decorator for enabling children support for AbstractEntities.
 * Defines methods declared in ContainsChildrenInterface.
 *
 * @see     \App\Orm\Entity\Contracts\ContainsChildrenInterface
 *
 * @package App\Orm\Entity\Decorators
 */
class ContainerEntityDecorator extends AbstractDecorator implements ContainsChildrenInterface
{
    /**
     * @var \App\Orm\Persistence\ReferenceAwareEntityCollection Reference on instance of the children property.
     */
    protected ReferenceAwareEntityCollection $children;

    /**
     * Create array representation of the current entity.
     *
     * @return array
     */
    public function convertToArray() : array
    {
        $base = parent::convertToArray();

        foreach ($this->getChildren() as $child) {
            $base['children'][] = $child->convertToArray();
        }

        return $base;
    }

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
