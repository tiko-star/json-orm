<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use App\Orm\Definition\Exception\DefinitionCompilationException;

use function sprintf;

class EntityDefinitionBuilder
{
    protected string $name = '';

    protected string $type = EntityDefinition::ENTITY_TYPE_WIDGET;

    protected bool $containsChildren = false;

    protected array $propertyDefinitions = [];

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name) : EntityDefinitionBuilder
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type) : EntityDefinitionBuilder
    {
        $this->type = $type;

        return $this;
    }

    public function enableChildrenSupport() : EntityDefinitionBuilder
    {
        $this->containsChildren = true;

        return $this;
    }

    public function disableChildrenSupport() : EntityDefinitionBuilder
    {
        $this->containsChildren = false;

        return $this;
    }

    /**
     * @return array
     */
    public function getPropertyDefinitions() : array
    {
        return $this->propertyDefinitions;
    }

    /**
     * @param \App\Orm\Definition\PropertyDefinition $propertyDefinition
     */
    public function addPropertyDefinition(PropertyDefinition $propertyDefinition) : void
    {
        $this->propertyDefinitions[$propertyDefinition->getPropertyName()] = $propertyDefinition;
    }

    /**
     * @return \App\Orm\Definition\EntityDefinition
     * @throws \App\Orm\Definition\Exception\DefinitionCompilationException
     */
    public function getEntityDescription() : EntityDefinition
    {
        $isWidget = false;
        $isWidgetItem = false;

        if ($this->type === EntityDefinition::ENTITY_TYPE_WIDGET) {
            $isWidget = true;
            $isWidgetItem = false;
        }

        if ($this->type === EntityDefinition::ENTITY_TYPE_WIDGET_ITEM) {
            $isWidget = false;
            $isWidgetItem = true;
        }

        if (($isWidget || $isWidgetItem) === false) {
            throw new DefinitionCompilationException(
                sprintf('Invalid entity type in description: [%s]', $this->type)
            );
        }

        return new EntityDefinition(
            $this->getName(),
            $isWidget,
            $isWidgetItem,
            $this->containsChildren,
            $this->getPropertyDefinitions()
        );
    }
}
