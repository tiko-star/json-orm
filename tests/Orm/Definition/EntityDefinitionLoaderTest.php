<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Definition;

use App\Orm\Definition\EntityDefinition;
use App\Orm\Definition\EntityDefinitionLoader;
use Symfony\Component\Finder\Finder;

class EntityDefinitionLoaderTest extends DefinitionAssertions
{
    public function testLoadDefinitions_WithGivenPath_LoadsAllJsonDefinitions() : void
    {
        $loader = $this->createLoaderInstance();

        $definitions = $loader->loadDefinitions(__DIR__.'/definitions');

        $this->assertCount(2, $definitions);
        $this->assertInstanceOf(EntityDefinition::class, $definitions['button']);
        $this->assertInstanceOf(EntityDefinition::class, $definitions['title']);

        $this->assertButtonDefinition($definitions['button']);
        $this->assertTitleDefinition($definitions['title']);
    }

    protected function createLoaderInstance() : EntityDefinitionLoader
    {
        return new EntityDefinitionLoader(new Finder());
    }
}
