<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Definition;

use App\Orm\Definition\EntityDefinition;
use App\Orm\Definition\PropertyDefinition;
use PHPUnit\Framework\TestCase;

abstract class DefinitionAssertions extends TestCase
{
    protected function assertButtonDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('button', $definition->getName());
        $this->assertTrue($definition->isWidget());
        $this->assertFalse($definition->isWidgetItem());
        $this->assertFalse($definition->containsChildren());
        $this->assertCount(2, $definition->getPropertyDefinitionList());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertEquals('text', $propertyDefinitionList['text']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDefinitionList['text']->getPropertyType());

        $this->assertEquals('link', $propertyDefinitionList['link']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDefinitionList['link']->getPropertyType());
    }

    protected function assertTitleDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('title', $definition->getName());
        $this->assertTrue($definition->isWidget());
        $this->assertFalse($definition->isWidgetItem());
        $this->assertFalse($definition->containsChildren());
        $this->assertCount(1, $definition->getPropertyDefinitionList());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertEquals('text', $propertyDefinitionList['text']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDefinitionList['text']->getPropertyType());
    }

    protected function assertGalleryDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('gallery', $definition->getName());
        $this->assertTrue($definition->isWidget());
        $this->assertFalse($definition->isWidgetItem());
        $this->assertTrue($definition->containsChildren());
        $this->assertCount(3, $definition->getPropertyDefinitionList());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertEquals('title', $propertyDefinitionList['title']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDefinitionList['title']->getPropertyType());

        $this->assertEquals('rows', $propertyDefinitionList['rows']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDefinitionList['rows']->getPropertyType());

        $this->assertEquals('cols', $propertyDefinitionList['cols']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDefinitionList['cols']->getPropertyType());
    }

    protected function assertGalleryItemDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('galleryItem', $definition->getName());
        $this->assertFalse($definition->isWidget());
        $this->assertTrue($definition->isWidgetItem());
        $this->assertFalse($definition->containsChildren());
        $this->assertCount(1, $definition->getPropertyDefinitionList());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertEquals('image', $propertyDefinitionList['image']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDefinitionList['image']->getPropertyType());
    }
}
