<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use App\Orm\Definition\Exception\DefinitionNotFoundException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function sprintf;

/**
 * Class EntityDefinitionProvider provides mechanism for retrieving EntityDefinition definition instances.
 * After first time retrieval it caches the definition instances for later use.
 *
 * @package App\Orm\Definition
 */
class EntityDefinitionProvider
{
    /**
     * @var string Path from where to read definitions.
     */
    protected string $definitionsPath;

    /**
     * @var \App\Orm\Definition\EntityDefinitionLoader Reference on EntityDefinitionLoader instance.
     */
    protected EntityDefinitionLoader $definitionLoader;

    /**
     * @var \Psr\Cache\CacheItemPoolInterface Reference on CacheItemPoolInterface instance.
     */
    protected CacheItemPoolInterface $cache;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface Reference on PropertyAccessorInterface instance.
     */
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
     * Retrieve definition for given entity name.
     * First of all try to fetch it from cache.
     * If the definition is missing in the cache, try to load it from the source and after store it in the cache.
     *
     * @param string $entityName Name of the requested Entity.
     *
     * @return \App\Orm\Definition\EntityDefinition
     *
     * @phpstan-ignore-next-line
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

        // Here we are loading all the definitions from the source.
        // To store them in the cache for later use.
        $entityDefinitions = $this->definitionLoader->loadDefinitions($this->definitionsPath);
        $entityDefinition = $this->propertyAccessor->getValue($entityDefinitions, "[$entityName]");

        if ($entityDefinition === null) {
            throw new DefinitionNotFoundException(
                sprintf('There is no definition for entity: %s', $entityName)
            );
        }

        $this->cacheDefinitions($entityDefinitions);

        return $entityDefinition;
    }

    /**
     * Store definition instances in the cache.
     *
     * @param \App\Orm\Definition\EntityDefinition[] $entityDefinitions List of EntityDefinition instances.
     *
     * @phpstan-ignore-next-line
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
