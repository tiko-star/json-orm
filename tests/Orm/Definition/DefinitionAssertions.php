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
        $this->assertCount(2, $definition->getPropertyDescriptionList());

        $propertyDescriptionList = $definition->getPropertyDescriptionList();

        $this->assertEquals('text', $propertyDescriptionList['text']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDescriptionList['text']->getPropertyType());

        $this->assertEquals('link', $propertyDescriptionList['link']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDescriptionList['link']->getPropertyType());
    }

    protected function assertTitleDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('title', $definition->getName());
        $this->assertTrue($definition->isWidget());
        $this->assertFalse($definition->isWidgetItem());
        $this->assertFalse($definition->containsChildren());
        $this->assertCount(1, $definition->getPropertyDescriptionList());

        $propertyDescriptionList = $definition->getPropertyDescriptionList();

        $this->assertEquals('text', $propertyDescriptionList['text']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDescriptionList['text']->getPropertyType());
    }

    protected function assertGalleryDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('gallery', $definition->getName());
        $this->assertTrue($definition->isWidget());
        $this->assertFalse($definition->isWidgetItem());
        $this->assertTrue($definition->containsChildren());
        $this->assertCount(3, $definition->getPropertyDescriptionList());

        $propertyDescriptionList = $definition->getPropertyDescriptionList();

        $this->assertEquals('title', $propertyDescriptionList['title']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDescriptionList['title']->getPropertyType());

        $this->assertEquals('rows', $propertyDescriptionList['rows']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDescriptionList['rows']->getPropertyType());

        $this->assertEquals('cols', $propertyDescriptionList['cols']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDescriptionList['cols']->getPropertyType());
    }

    protected function assertGalleryItemDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('galleryItem', $definition->getName());
        $this->assertFalse($definition->isWidget());
        $this->assertTrue($definition->isWidgetItem());
        $this->assertFalse($definition->containsChildren());
        $this->assertCount(1, $definition->getPropertyDescriptionList());

        $propertyDescriptionList = $definition->getPropertyDescriptionList();

        $this->assertEquals('image', $propertyDescriptionList['image']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDescriptionList['image']->getPropertyType());
    }
}
