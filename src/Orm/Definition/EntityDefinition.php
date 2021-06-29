<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Object Oriented representation of the Entity definitions.
 *
 * @package App\Orm\Definition
 */
class EntityDefinition
{
    /**
     * @var string Type of the Entity.
     */
    protected string $type;

    /**
     * @var bool Define whether an Entity can contain child Entities or not.
     */
    protected bool $containsChildren;

    /**
     * @var bool Define whether an Entity can contain data validation ruleset or not.
     */
    protected bool $containsValidation;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection List of PropertyDefinition instances.
     */
    protected ArrayCollection $propertyDefinitionList;

    public function __construct(string $type, bool $containsChildren, bool $containsValidation, ArrayCollection $propertyDefinitionList)
    {
        $this->type = $type;
        $this->containsChildren = $containsChildren;
        $this->containsValidation = $containsValidation;
        $this->propertyDefinitionList = $propertyDefinitionList;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function containsChildren() : bool
    {
        return $this->containsChildren;
    }

    /**
     * @return bool
     */
    public function containsValidation() : bool
    {
        return $this->containsValidation;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPropertyDefinitionList() : ArrayCollection
    {
        return $this->propertyDefinitionList;
    }
}
