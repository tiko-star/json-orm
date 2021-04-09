<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Definition;

use App\Orm\Definition\DefinitionCompiler;
use App\Orm\Definition\EntityDefinition;
use App\Orm\Definition\EntityDefinitionLoader;
use Symfony\Component\Finder\Finder;

class EntityDefinitionLoaderTest extends DefinitionAssertions
{
    public function testLoadDefinitions_WithGivenPath_LoadsAllJsonDefinitions() : void
    {
        $loader = $this->createLoaderInstance();

        $definitions = $loader->loadDefinitions(__DIR__.'/definitions');

        $this->assertCount(7, $definitions);
        $this->assertInstanceOf(EntityDefinition::class, $definitions['button']);
        $this->assertInstanceOf(EntityDefinition::class, $definitions['title']);
        $this->assertInstanceOf(EntityDefinition::class, $definitions['gallery']);
        $this->assertInstanceOf(EntityDefinition::class, $definitions['galleryItem']);
        $this->assertInstanceOf(EntityDefinition::class, $definitions['block']);
        $this->assertInstanceOf(EntityDefinition::class, $definitions['row']);
        $this->assertInstanceOf(EntityDefinition::class, $definitions['column']);

        $this->assertButtonDefinition($definitions['button']);
        $this->assertTitleDefinition($definitions['title']);
        $this->assertGalleryDefinition($definitions['gallery']);
        $this->assertGalleryItemDefinition($definitions['galleryItem']);
        $this->assertBlockDefinition($definitions['block']);
        $this->assertRowDefinition($definitions['row']);
        $this->assertColumnDefinition($definitions['column']);
    }

    protected function createLoaderInstance() : EntityDefinitionLoader
    {
        return new EntityDefinitionLoader(new Finder(), new DefinitionCompiler());
    }
}
