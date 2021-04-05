<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use App\Orm\Definition\Exception\DefinitionCompilationException;

use function sprintf;

/**
 * Class EntityDefinitionBuilder creates EntityDefinition instances based on the setup.
 *
 * @package App\Orm\Definition
 */
class EntityDefinitionBuilder
{
    /**
     * @var string Name of the Entity.
     */
    protected string $name = '';

    /**
     * @var string Type of the Entity.
     */
    protected string $type = EntityDefinition::ENTITY_TYPE_WIDGET;

    /**
     * @var bool Define whether an Entity can contain children or not.
     */
    protected bool $containsChildren = false;

    /**
     * @var array List of definitions of Entity properties.
     */
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

    /**
     * Enable for an Entity to be able to contain child Entities.
     *
     * @return $this
     */
    public function enableChildrenSupport() : EntityDefinitionBuilder
    {
        $this->containsChildren = true;

        return $this;
    }

    /**
     * Disable for an Entity to be able to contain child Entities.
     *
     * @return $this
     */
    public function disableChildrenSupport() : EntityDefinitionBuilder
    {
        $this->containsChildren = false;

        return $this;
    }

    /**
     * @return \App\Orm\Definition\PropertyDefinition[]
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
     * Create EntityDefinition instance based on previously defined settings.
     *
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