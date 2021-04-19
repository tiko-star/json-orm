<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Definition;

use App\Orm\Definition\DefinitionCompiler;
use App\Orm\Definition\EntityDefinition;
use App\Orm\Definition\EntityDefinitionLoader;
use App\Orm\Definition\EntityDefinitionProvider;
use App\Orm\Definition\Exception\DefinitionNotFoundException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
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

    public function testFetchEntityDefinition_ForBlock_ReturnsDefinitionInstance() : void
    {
        $provider = $this->createProviderInstance();
        $definition = $provider->fetchEntityDefinition('block');

        $this->assertBlockDefinition($definition);
    }

    public function testFetchEntityDefinition_ForRow_ReturnsDefinitionInstance() : void
    {
        $provider = $this->createProviderInstance();
        $definition = $provider->fetchEntityDefinition('row');

        $this->assertRowDefinition($definition);
    }

    public function testFetchEntityDefinition_ForColumn_ReturnsDefinitionInstance() : void
    {
        $provider = $this->createProviderInstance();
        $definition = $provider->fetchEntityDefinition('column');

        $this->assertColumnDefinition($definition);
    }

    public function testFetchEntityDefinition_WhenDefinitionSourceIsMissing_ThrowsException() : void
    {
        $provider = $this->createProviderInstance();

        $this->expectException(DefinitionNotFoundException::class);
        $this->expectExceptionMessage('There is no definition for entity: spaceship');

        $provider->fetchEntityDefinition('spaceship');
    }

    public function testFetchEntityDefinition_FirstLooksInCacheForDefinition() : void
    {
        $cache = $this->createMock(PhpFilesAdapter::class);
        $item = $this->createMock(CacheItemInterface::class);

        // Set up the expectation for the isHit() method to be called only once
        $item
            ->expects($this->once())
            ->method('isHit')
            ->willReturn(true);

        // Set up the expectation for the get() method to be called only once
        $item
            ->expects($this->once())
            ->method('get')
            ->willReturn(new EntityDefinition('button', true, false, false, false, false, []));

        // Set up the expectation for the getItem() method
        // to be called only once and with the string 'button' as its parameter.
        $cache
            ->expects($this->once())
            ->method('getItem')
            ->with($this->equalTo('button'))
            ->willReturn($item);

        // Create the EntityDefinitionProvider instance and attach the mocked
        // PhpFilesAdapter object to it.
        $provider = $this->createProviderInstance($cache);
        $provider->fetchEntityDefinition('button');
    }

    protected function createProviderInstance(CacheItemPoolInterface $pool = null) : EntityDefinitionProvider
    {
        return new EntityDefinitionProvider(
            __DIR__.'/definitions',
            new EntityDefinitionLoader(new Finder(), new DefinitionCompiler()),
            $pool ?? $this->createCacheMock()
        );
    }

    protected function createCacheMock() : AbstractAdapter
    {
        $item = $this->createStub(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);

        $cache = $this->createStub(PhpFilesAdapter::class);
        $cache->method('getItem')->willReturn($item);
        $cache->method('save')->willReturn(false);

        return $cache;
    }
}
