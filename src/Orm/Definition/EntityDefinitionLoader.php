<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use JsonException;
use App\Orm\Definition\Exception\DefinitionCompilationException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function json_decode;
use function is_array;
use function sprintf;

/**
 * Class EntityDefinitionLoader provides mechanism for loading all the definitions from the source.
 * After loading it compiles the definition data into appropriate EntityDefinition instances.
 *
 * @package App\Orm\Definition
 */
class EntityDefinitionLoader
{
    /**
     * @var \Symfony\Component\Finder\Finder Reference on Finder instance.
     */
    protected Finder $finder;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface Reference on PropertyAccessorInterface instance.
     */
    protected PropertyAccessorInterface $propertyAccessor;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidIndex()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();
    }

    /**
     * Read all the definitions from the given source path.
     * Create appropriate EntityDefinition instances.
     *
     * @param string $path Path from where to load the definitions.
     *
     * @return \App\Orm\Definition\EntityDefinition[]
     * @throws \App\Orm\Definition\Exception\DefinitionCompilationException
     */
    public function loadDefinitions(string $path) : array
    {
        // The definitions must be JSON documents.
        $definitions = $this->finder->name('*.json')->files()->in($path);
        $entityDefinitions = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $definition */
        foreach ($definitions as $definition) {
            $content = $definition->getContents();
            $entityDefinition = $this->createDefinitionInstanceFromString($content);
            $entityDefinitions[$entityDefinition->getName()] = $entityDefinition;
        }

        return $entityDefinitions;
    }

    /**
     * Compile definition string into EntityDefinition instance.
     *
     * @param string $definitionString JSON representation of the definition.
     *
     * @return \App\Orm\Definition\EntityDefinition
     * @throws \App\Orm\Definition\Exception\DefinitionCompilationException
     */
    protected function createDefinitionInstanceFromString(string $definitionString) : EntityDefinition
    {
        try {
            $definitionData = json_decode($definitionString, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new DefinitionCompilationException(
                sprintf('Invalid JSON definition: %s', $e->getMessage())
            );
        }

        $name = $this->fetchPropertyFromDefinitionData('name', $definitionData);
        $type = $this->fetchPropertyFromDefinitionData('type', $definitionData);
        $containsChildren = $this->fetchPropertyFromDefinitionData('containsChildren', $definitionData);
        $properties = $this->fetchPropertyFromDefinitionData('properties', $definitionData, false);

        $definitionBuilder = $this->createEntityDefinitionBuilder();
        $definitionBuilder
            ->setName($name)
            ->setType($type);

        if ($containsChildren) {
            $definitionBuilder->enableChildrenSupport();
        }

        // If there are property definitions also compile them.
        if (is_array($properties)) {
            foreach ($properties as $property) {
                $name = $this->fetchPropertyFromDefinitionData('name', $property);
                $type = $this->fetchPropertyFromDefinitionData('type', $property);

                $definitionBuilder->addPropertyDefinition(
                    new PropertyDefinition($name, $type)
                );
            }
        }

        return $definitionBuilder->getEntityDescription();
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
        if ($required && !$this->propertyAccessor->isReadable($data, "[$property]")) {
            throw new DefinitionCompilationException(
                sprintf('Required definition property is missing: %s', $property)
            );
        }

        return $this->propertyAccessor->getValue($data, "[$property]");
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
