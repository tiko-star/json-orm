<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Definition;

use App\Orm\Definition\EntityDefinitionLoader;
use App\Orm\Definition\EntityDefinitionProvider;
use App\Orm\Definition\Exception\DefinitionNotFoundException;
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

    public function testFetchEntityDefinition_ForGallery_ReturnsDefinitionInstance() : void
    {
        $provider = $this->createProviderInstance();
        $definition = $provider->fetchEntityDefinition('gallery');

        $this->assertGalleryDefinition($definition);
    }

    public function testFetchEntityDefinition_ForGalleryItem_ReturnsDefinitionInstance() : void
    {
        $provider = $this->createProviderInstance();
        $definition = $provider->fetchEntityDefinition('galleryItem');

        $this->assertGalleryItemDefinition($definition);
    }

    public function testFetchEntityDefinition_WhenDefinitionSourceIsMissing_ThrowsException() : void
    {
        $provider = $this->createProviderInstance();

        $this->expectException(DefinitionNotFoundException::class);
        $this->expectExceptionMessage('There is no definition for entity: spaceship');

        $provider->fetchEntityDefinition('spaceship');
    }

    protected function createProviderInstance() : EntityDefinitionProvider
    {
        return new EntityDefinitionProvider(
            __DIR__.'/definitions',
            new EntityDefinitionLoader(new Finder()),
            new PhpFilesAdapter('definitions', 0, __DIR__.'/cache')
        );
    }
}
