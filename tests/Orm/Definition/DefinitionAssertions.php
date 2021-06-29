<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Definition;

use App\Orm\Definition\EntityDefinition;
use App\Orm\Definition\PropertyDefinition;
use PHPUnit\Framework\TestCase;

/**
 * Base class for different EntityDefinition assertions.
 *
 * @package App\Tests\Orm\Definition
 */
abstract class DefinitionAssertions extends TestCase
{
    protected function assertButtonDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('button', $definition->getType());
        $this->assertFalse($definition->containsChildren());
        $this->assertFalse($definition->containsValidation());
        $this->assertCount(2, $definition->getPropertyDefinitionList());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertEquals('text', $propertyDefinitionList['text']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDefinitionList['text']->getPropertyType());
        $this->assertTrue($propertyDefinitionList['text']->isTranslatable());

        $this->assertEquals('link', $propertyDefinitionList['link']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDefinitionList['link']->getPropertyType());
        $this->assertFalse($propertyDefinitionList['link']->isTranslatable());
    }

    protected function assertTitleDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('title', $definition->getType());
        $this->assertFalse($definition->containsChildren());
        $this->assertFalse($definition->containsValidation());
        $this->assertCount(1, $definition->getPropertyDefinitionList());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertEquals('text', $propertyDefinitionList['text']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDefinitionList['text']->getPropertyType());
        $this->assertTrue($propertyDefinitionList['text']->isTranslatable());
    }

    protected function assertGalleryDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('gallery', $definition->getType());
        $this->assertTrue($definition->containsChildren());
        $this->assertTrue($definition->containsValidation());
        $this->assertCount(3, $definition->getPropertyDefinitionList());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertEquals('title', $propertyDefinitionList['title']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDefinitionList['title']->getPropertyType());
        $this->assertTrue($propertyDefinitionList['title']->isTranslatable());

        $this->assertEquals('rows', $propertyDefinitionList['rows']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDefinitionList['rows']->getPropertyType());
        $this->assertFalse($propertyDefinitionList['rows']->isTranslatable());

        $this->assertEquals('cols', $propertyDefinitionList['cols']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDefinitionList['cols']->getPropertyType());
        $this->assertFalse($propertyDefinitionList['cols']->isTranslatable());
    }

    protected function assertGalleryItemDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('galleryItem', $definition->getType());
        $this->assertFalse($definition->containsChildren());
        $this->assertFalse($definition->containsValidation());
        $this->assertCount(1, $definition->getPropertyDefinitionList());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertEquals('image', $propertyDefinitionList['image']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDefinitionList['image']->getPropertyType());
        $this->assertFalse($propertyDefinitionList['image']->isTranslatable());
    }

    public function assertBlockDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('block', $definition->getType());
        $this->assertGridDefinition($definition);
    }

    public function assertRowDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('row', $definition->getType());
        $this->assertGridDefinition($definition);
    }

    public function assertColumnDefinition(EntityDefinition $definition) : void
    {
        $this->assertEquals('column', $definition->getType());
        $this->assertGridDefinition($definition);
    }

    private function assertGridDefinition(EntityDefinition $definition) : void
    {
        $this->assertTrue($definition->containsChildren());
    }
}
