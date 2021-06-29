<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use App\Orm\Definition\Exception\DefinitionCompilationException;

use JsonException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function json_decode;
use function is_array;
use function sprintf;

/**
 * Compiles string representation of definition into \App\Orm\Definition\EntityDefinition instance.
 *
 * @package App\Orm\Definition
 */
class DefinitionCompiler
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface Reference on PropertyAccessorInterface instance.
     */
    protected PropertyAccessorInterface $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidIndex()
            ->disableExceptionOnInvalidPropertyPath()
            ->enableMagicCall()
            ->getPropertyAccessor();
    }

    /**
     * Compile definition string into EntityDefinition instance.
     *
     * @param string $definitionString JSON representation of the definition.
     *
     * @return \App\Orm\Definition\EntityDefinition
     * @throws \App\Orm\Definition\Exception\DefinitionCompilationException
     */
    public function createDefinitionInstanceFromString(string $definitionString) : EntityDefinition
    {
        try {
            $definitionData = json_decode($definitionString, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new DefinitionCompilationException(
                sprintf('Invalid JSON definition: %s', $e->getMessage())
            );
        }

        $type = $this->fetchPropertyFromDefinitionData('type', $definitionData);
        $containsChildren = $this->fetchPropertyFromDefinitionData('containsChildren', $definitionData);
        $containsValidation = $this->fetchPropertyFromDefinitionData('containsValidation', $definitionData, false);
        $properties = $this->fetchPropertyFromDefinitionData('properties', $definitionData, false);

        $definitionBuilder = $this->createEntityDefinitionBuilder();
        $definitionBuilder
            ->setType($type);

        if ($containsChildren) {
            $definitionBuilder->enableChildrenSupport();
        }

        if ($containsValidation) {
            $definitionBuilder->enableValidationSupport();
        }

        // If there are property definitions also compile them.
        if (is_array($properties)) {
            foreach ($properties as $property) {
                $name = $this->fetchPropertyFromDefinitionData('name', $property);
                $type = $this->fetchPropertyFromDefinitionData('type', $property);
                $isTranslatable = $this->fetchPropertyFromDefinitionData('isTranslatable', $property, false);

                $definitionBuilder->addPropertyDefinition(
                    new PropertyDefinition($name, $type, $isTranslatable ?? true)
                );
            }
        }

        return $definitionBuilder->getEntityDefinition();
    }

    /**
     * Try to fetch definition property from given data.
     *
     * @param string $property Property name to fetch.
     * @param array  $data     Data where to look for the the property.
     * @param bool   $required If set to true, fail in case of property lack.
     *
     * @return mixed|null
     * @throws \App\Orm\Definition\Exception\DefinitionCompilationException
     */
    protected function fetchPropertyFromDefinitionData(string $property, array $data, bool $required = true)
    {
        $value = $this->propertyAccessor->getValue($data, "[$property]");

        if ($required && $value === null) {
            throw new DefinitionCompilationException(
                sprintf('Required definition property is missing: %s', $property)
            );
        }

        return $value;
    }

    /**
     * Create instance of EntityDefinitionBuilder without children support by default.
     *
     * @return \App\Orm\Definition\EntityDefinitionBuilder
     */
    protected function createEntityDefinitionBuilder() : EntityDefinitionBuilder
    {
        $definitionBuilder = new EntityDefinitionBuilder();
        $definitionBuilder->disableChildrenSupport();

        return $definitionBuilder;
    }
}
