<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

/**
 * Object Oriented representation of the Entity's property definitions.
 *
 * @package App\Orm\Definition
 */
class PropertyDefinition
{
    const PROPERTY_TYPE_INT = 'INT';
    const PROPERTY_TYPE_DOUBLE = 'DOUBLE';
    const PROPERTY_TYPE_STRING = 'STRING';
    const PROPERTY_TYPE_ARRAY = 'ARRAY';
    const PROPERTY_TYPE_ANY = 'ANY';
    const PROPERTY_TYPE_BOOL = 'BOOL';

    /**
     * @var string Name of the property.
     */
    protected string $propertyName;

    /**
     * @var string Scalar type of the property.
     */
    protected string $propertyType;

    /**
     * @var bool Define whether property is translatable is not.
     */
    protected bool $isTranslatable;

    public function __construct(string $propertyName, string $propertyType, bool $isTranslatable = true)
    {
        $this->propertyName = $propertyName;
        $this->propertyType = $propertyType;
        $this->isTranslatable = $isTranslatable;
    }

    /**
     * @return string
     */
    public function getPropertyName() : string
    {
        return $this->propertyName;
    }

    /**
     * @return string
     */
    public function getPropertyType() : string
    {
        return $this->propertyType;
    }

    /**
     * @return bool
     */
    public function isTranslatable() : bool
    {
        return $this->isTranslatable;
    }
}
