<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

/**
 * Object Oriented representation of the Entity definitions.
 *
 * @package App\Orm\Definition
 */
class EntityDefinition
{
    const ENTITY_TYPE_WIDGET = 'WIDGET';
    const ENTITY_TYPE_WIDGET_ITEM = 'WIDGET_ITEM';
    const ENTITY_TYPE_GRID = [
        'BLOCK',
        'ROW',
        'COLUMN'
    ];

    /**
     * @var string Name of the Entity.
     */
    protected string $name;

    /**
     * @var bool Define whether an Entity is instance of Widget or not.
     */
    protected bool $isWidget;

    /**
     * @var bool Define whether an Entity is instance of WidgetItem or not.
     */
    protected bool $isWidgetItem;

    /**
     * @var bool Define whether an Entity is instance of Grid representation or not.
     */
    protected bool $isGrid;

    /**
     * @var bool Define whether an Entity can contain child Entities or not.
     */
    protected bool $containsChildren;

    /**
     * @var \App\Orm\Definition\PropertyDefinition[] List of PropertyDefinition instances.
     */
    protected array $propertyDefinitionList;

    public function __construct(string $name, bool $isWidget, bool $isWidgetItem, bool $isGrid, bool $containsChildren, array $propertyDefinitionList)
    {
        $this->name = $name;
        $this->isWidget = $isWidget;
        $this->isWidgetItem = $isWidgetItem;
        $this->isGrid = $isGrid;
        $this->containsChildren = $containsChildren;
        $this->propertyDefinitionList = $propertyDefinitionList;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isWidget() : bool
    {
        return $this->isWidget;
    }

    /**
     * @return bool
     */
    public function isWidgetItem() : bool
    {
        return $this->isWidgetItem;
    }

    /**
     * @return bool
     */
    public function isGrid() : bool
    {
        return $this->isGrid;
    }

    /**
     * @param bool $isGrid
     */
    public function setIsGrid(bool $isGrid) : void
    {
        $this->isGrid = $isGrid;
    }

    /**
     * @return bool
     */
    public function containsChildren() : bool
    {
        return $this->containsChildren || $this->isGrid();
    }

    /**
     * @return \App\Orm\Definition\PropertyDefinition[]
     */
    public function getPropertyDefinitionList() : array
    {
        return $this->propertyDefinitionList;
    }
}
