<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Definition;

use App\Orm\Definition\EntityDefinitionLoader;
use App\Orm\Definition\EntityDefinitionProvider;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Finder\Finder;

class EntityDefinitionProviderTest extends DefinitionAssertions
{
    public function testFetchEntityDefinition_ForButton_ReturnsDefinitionInstance() : void
    {
        $provider = $this->createProviderInstance();
        $definition = $provider->fetchEntityDefinition('button');

        $this->assertButtonDefinition($definition);
    }

    public function testFetchEntityDefinition_ForTitle_ReturnsDefinitionInstance() : void
    {
        $provider = $this->createProviderInstance();
        $definition = $provider->fetchEntityDefinition('title');

        $this->assertTitleDefinition($definition);
    }

    /**
     * @return \App\Orm\Definition\EntityDefinitionProvider
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function createProviderInstance() : EntityDefinitionProvider
    {
        return new EntityDefinitionProvider(
            __DIR__.'/definitions',
            new EntityDefinitionLoader(new Finder()),
            new PhpFilesAdapter('definitions', 0, __DIR__.'/cache')
        );
    }
}
