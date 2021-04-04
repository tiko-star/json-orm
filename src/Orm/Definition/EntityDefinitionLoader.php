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

class EntityDefinitionLoader
{
    protected Finder $finder;

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
     * @param string $path
     *
     * @return \App\Orm\Definition\EntityDefinition[]
     * @throws \App\Orm\Definition\Exception\DefinitionCompilationException
     */
    public function loadDefinitions(string $path) : array
    {
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
     * @param string $definitionString
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
     * @param string $property
     * @param array  $data
     * @param bool   $required
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

    protected function createEntityDefinitionBuilder() : EntityDefinitionBuilder
    {
        $definitionBuilder = new EntityDefinitionBuilder();
        $definitionBuilder->disableChildrenSupport();

        return $definitionBuilder;
    }
}
