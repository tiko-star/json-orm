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

    public function __construct(string $propertyName, string $propertyType)
    {
        $this->propertyName = $propertyName;
        $this->propertyType = $propertyType;
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
}
