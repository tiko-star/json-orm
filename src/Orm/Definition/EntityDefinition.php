<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

class EntityDefinition
{
    const ENTITY_TYPE_WIDGET = 'WIDGET';
    const ENTITY_TYPE_WIDGET_ITEM = 'WIDGET_ITEM';

    /**
     * @var string Name of the Entity.
     */
    protected string $name;

    /**
     * @var bool Define whether an Entity is instance of AbstractWidget or not.
     */
    protected bool $isWidget;

    /**
     * @var bool Define whether an Entity is instance of AbstractWidgetItem or not.
     */
    protected bool $isWidgetItem;

    /**
     * @var bool Define whether an Entity can contain child Entities or not.
     */
    protected bool $containsChildren;

    /**
     * @var \App\Orm\Definition\PropertyDefinition[] List of PropertyDefinition instances.
     */
    protected array $propertyDescriptionList;

    public function __construct(string $name, bool $isWidget, bool $isWidgetItem, bool $containsChildren, array $propertyDescriptionList)
    {
        $this->name = $name;
        $this->isWidget = $isWidget;
        $this->isWidgetItem = $isWidgetItem;
        $this->containsChildren = $containsChildren;
        $this->propertyDescriptionList = $propertyDescriptionList;
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
    public function containsChildren() : bool
    {
        return $this->containsChildren;
    }

    /**
     * @return \App\Orm\Definition\PropertyDefinition[]
     */
    public function getPropertyDescriptionList() : array
    {
        return $this->propertyDescriptionList;
    }
}
