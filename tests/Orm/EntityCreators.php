<?php

declare(strict_types = 1);

namespace App\Tests\Orm;

use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\AbstractWidget;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use App\Orm\Entity\Utils\HandleChildrenTrait;

/**
 * Trait EntityCreators defines factory methods for creating instances of Entities and Widgets.
 *
 * @package App\Tests\Orm
 */
trait EntityCreators
{
    /**
     * Create instance of ENTITY_TYPE_GRID type AbstractEntity.
     *
     * @return \App\Orm\Entity\AbstractEntity
     * @see \App\Orm\Definition\EntityDefinition::ENTITY_TYPE_GRID
     */
    protected function createGridEntity() : AbstractEntity
    {
        return new class extends AbstractEntity implements ContainsChildrenInterface {
            use HandleChildrenTrait;
        };
    }

    /**
     * Create instance of ENTITY_TYPE_WIDGET type AbstractEntity.
     *
     * @return \App\Orm\Entity\AbstractWidget
     * @see \App\Orm\Definition\EntityDefinition::ENTITY_TYPE_WIDGET
     */
    protected function createContainerWidget() : AbstractWidget
    {
        return new class extends AbstractWidget implements ContainsChildrenInterface {
            use HandleChildrenTrait;
        };
    }

    /**
     * Create instance of ENTITY_TYPE_WIDGET_ITEM type AbstractEntity.
     *
     * @return \App\Orm\Entity\AbstractWidget
     * @see \App\Orm\Definition\EntityDefinition::ENTITY_TYPE_WIDGET_ITEM
     */
    protected function createSimpleWidget() : AbstractWidget
    {
        return new class extends AbstractWidget {
        };
    }
}
