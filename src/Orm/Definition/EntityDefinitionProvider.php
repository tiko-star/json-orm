<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use App\Orm\Definition\Exception\DefinitionNotFoundException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function sprintf;

class EntityDefinitionProvider
{
    protected string $definitionsPath;

    protected EntityDefinitionLoader $definitionLoader;

    protected CacheItemPoolInterface $cache;

    protected PropertyAccessorInterface $propertyAccessor;

    public function __construct(string $definitionsPath, EntityDefinitionLoader $definitionLoader, CacheItemPoolInterface $cache)
    {
        $this->definitionsPath = $definitionsPath;
        $this->definitionLoader = $definitionLoader;
        $this->cache = $cache;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidIndex()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();
    }

    /**
     * @param string $entityName
     *
     * @return \App\Orm\Definition\EntityDefinition
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \App\Orm\Definition\Exception\DefinitionCompilationException
     * @throws \App\Orm\Definition\Exception\DefinitionNotFoundException
     */
    public function fetchEntityDefinition(string $entityName) : EntityDefinition
    {
        $item = $this->cache->getItem($entityName);

        if ($item->isHit()) {
            return $item->get();
        }

        $entityDefinitions = $this->definitionLoader->loadDefinitions($this->definitionsPath);
        $this->cacheDefinitions($entityDefinitions);

        if (!$this->propertyAccessor->isReadable($entityDefinitions, "[$entityName]")) {
            throw new DefinitionNotFoundException(
                sprintf('There is no definition for entity: %s', $entityName)
            );
        }

        return $this->propertyAccessor->getValue($entityDefinitions, "[$entityName]");
    }

    /**
     * @param \App\Orm\Definition\EntityDefinition[] $entityDefinitions
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function cacheDefinitions(array $entityDefinitions) : void
    {
        foreach ($entityDefinitions as $entityName => $entityDefinition) {
            $item = $this->cache->getItem($entityName);
            $item->set($entityDefinition);
            $this->cache->save($item);
        }
    }
}
