<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use App\Orm\Definition\Exception\DefinitionCompilationException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EntityDefinitionBuilder creates EntityDefinition instances based on the setup.
 *
 * @package App\Orm\Definition
 */
class EntityDefinitionBuilder
{
    /**
     * @var string Type of the Entity.
     */
    protected string $type = '';

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
        if (empty($this->getType())) {
            throw new DefinitionCompilationException('Entity type is not set.');
        }

        return new EntityDefinition(
            $this->getType(),
            $this->containsChildren,
            $this->containsValidation,
            $this->getPropertyDefinitions()
        );
    }
}
