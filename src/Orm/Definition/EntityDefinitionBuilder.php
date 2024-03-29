<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use App\Orm\Definition\Exception\DefinitionCompilationException;
use Doctrine\Common\Collections\ArrayCollection;

use function sprintf;
use function in_array;

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
     * @var bool Define whether an Entity can contain data validation ruleset or not.
     */
    protected bool $containsValidation = false;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection List of definitions of Entity properties.
     */
    protected ArrayCollection $propertyDefinitions;

    public function __construct()
    {
        $this->propertyDefinitions = new ArrayCollection();
    }

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
     * Enable for an Entity ability to contain child Entities.
     *
     * @return \App\Orm\Definition\EntityDefinitionBuilder
     */
    public function enableChildrenSupport() : EntityDefinitionBuilder
    {
        $this->containsChildren = true;

        return $this;
    }

    /**
     * Disable for an Entity ability to contain child Entities.
     *
     * @return \App\Orm\Definition\EntityDefinitionBuilder
     */
    public function disableChildrenSupport() : EntityDefinitionBuilder
    {
        $this->containsChildren = false;

        return $this;
    }

    /**
     * Enable for an Entity ability to contain data validation ruleset.
     *
     * @return \App\Orm\Definition\EntityDefinitionBuilder
     */
    public function enableValidationSupport() : EntityDefinitionBuilder
    {
        $this->containsValidation = true;

        return $this;
    }

    /**
     * Disable for an Entity ability to contain data validation ruleset.
     *
     * @return \App\Orm\Definition\EntityDefinitionBuilder
     */
    public function disableValidationSupport() : EntityDefinitionBuilder
    {
        $this->containsValidation = false;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPropertyDefinitions() : ArrayCollection
    {
        return $this->propertyDefinitions;
    }

    /**
     * @param \App\Orm\Definition\PropertyDefinition $propertyDefinition
     *
     * @return \App\Orm\Definition\EntityDefinitionBuilder
     */
    public function addPropertyDefinition(PropertyDefinition $propertyDefinition) : EntityDefinitionBuilder
    {
        $this->propertyDefinitions[$propertyDefinition->getPropertyName()] = $propertyDefinition;

        return $this;
    }

    /**
     * Create EntityDefinition instance based on previously defined settings.
     *
     * @return \App\Orm\Definition\EntityDefinition
     * @throws \App\Orm\Definition\Exception\DefinitionCompilationException
     */
    public function getEntityDefinition() : EntityDefinition
    {
        $isWidget = false;
        $isWidgetItem = false;
        $isGrid = false;

        if ($this->type === EntityDefinition::ENTITY_TYPE_WIDGET) {
            $isWidget = true;
        }

        if ($this->type === EntityDefinition::ENTITY_TYPE_WIDGET_ITEM) {
            $isWidgetItem = true;
        }

        if (in_array($this->type, EntityDefinition::ENTITY_TYPE_GRID)) {
            $isGrid = true;
        }

        if (($isWidget || $isWidgetItem || $isGrid) === false) {
            throw new DefinitionCompilationException(
                sprintf('Invalid entity type in the definition: [%s]', $this->type)
            );
        }

        return new EntityDefinition(
            $this->getName(),
            $isWidget,
            $isWidgetItem,
            $isGrid,
            $this->containsChildren,
            $this->containsValidation,
            $this->getPropertyDefinitions()
        );
    }
}
