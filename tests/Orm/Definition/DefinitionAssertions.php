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
}
