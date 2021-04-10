<?php

declare(strict_types = 1);

namespace App\Tests\Orm;

use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Decorators\ContainerEntityDecorator;
use App\Orm\Entity\Grid;
use App\Orm\Entity\Widget;

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
        return new ContainerEntityDecorator(new Grid());
    }

    /**
     * Create instance of ENTITY_TYPE_WIDGET type AbstractEntity.
     * Decorates with children support.
     *
     * @return \App\Orm\Entity\Decorators\ContainerEntityDecorator
     * @see \App\Orm\Definition\EntityDefinition::ENTITY_TYPE_WIDGET
     */
    protected function createContainerWidget() : ContainerEntityDecorator
    {
        return new ContainerEntityDecorator(new Widget());
    }

    /**
     * Create instance of ENTITY_TYPE_WIDGET type AbstractEntity.
     *
     * @return \App\Orm\Entity\Widget
     * @see \App\Orm\Definition\EntityDefinition::ENTITY_TYPE_WIDGET_ITEM
     */
    protected function createSimpleWidget() : Widget
    {
        return new Widget();
    }
}
