<?php

declare(strict_types = 1);

namespace App\Orm\Definition;

use Symfony\Component\Finder\Finder;

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
     * @var \App\Orm\Definition\DefinitionCompiler Reference on DefinitionCompiler instance.
     */
    protected DefinitionCompiler $compiler;

    public function __construct(Finder $finder, DefinitionCompiler $compiler)
    {
        $this->finder = $finder;
        $this->compiler = $compiler;
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
            $entityDefinition = $this->compiler->createDefinitionInstanceFromString($content);
            $entityDefinitions[$entityDefinition->getType()] = $entityDefinition;
        }

        return $entityDefinitions;
    }
}
