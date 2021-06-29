<?php

namespace App\Tests\Orm\Definition;

use App\Orm\Definition\EntityDefinition;
use App\Orm\Definition\EntityDefinitionBuilder;
use App\Orm\Definition\Exception\DefinitionCompilationException;
use App\Orm\Definition\PropertyDefinition;
use PHPUnit\Framework\TestCase;

class EntityDefinitionBuilderTest extends TestCase
{
    public function testGetEntityDefinition_WithWidgetSetup_CreatesEntityDefinitionInstance() : void
    {
        $builder = $this->createBuilderInstance();

        $definition = $builder
            ->setType('button')
            ->enableChildrenSupport()
            ->addPropertyDefinition(new PropertyDefinition('text', PropertyDefinition::PROPERTY_TYPE_STRING))
            ->getEntityDefinition();

        $this->assertInstanceOf(EntityDefinition::class, $definition);
        $this->assertEquals('button', $definition->getType());
        $this->assertTrue($definition->containsChildren());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertCount(1, $propertyDefinitionList);
        $this->assertEquals('text', $propertyDefinitionList['text']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_STRING, $propertyDefinitionList['text']->getPropertyType());
    }

    public function testGetEntityDefinition_WithWidgetItemSetup_CreatesEntityDefinitionInstance() : void
    {
        $builder = $this->createBuilderInstance();

        $definition = $builder
            ->setType('slide')
            ->disableChildrenSupport()
            ->addPropertyDefinition(new PropertyDefinition('imageId', PropertyDefinition::PROPERTY_TYPE_INT))
            ->getEntityDefinition();

        $this->assertInstanceOf(EntityDefinition::class, $definition);
        $this->assertEquals('slide', $definition->getType());
        $this->assertFalse($definition->containsChildren());

        $propertyDefinitionList = $definition->getPropertyDefinitionList();

        $this->assertCount(1, $propertyDefinitionList);
        $this->assertEquals('imageId', $propertyDefinitionList['imageId']->getPropertyName());
        $this->assertEquals(PropertyDefinition::PROPERTY_TYPE_INT, $propertyDefinitionList['imageId']->getPropertyType());
    }

    public function testGetEntityDefinition_WhenTypeIsNotSet_ThrowsException() : void
    {
        $builder = $this->createBuilderInstance();

        $this->expectException(DefinitionCompilationException::class);
        $this->expectExceptionMessage('Entity type is not set.');

        $builder
            ->disableChildrenSupport()
            ->getEntityDefinition();
    }

    protected function createBuilderInstance() : EntityDefinitionBuilder
    {
        return new EntityDefinitionBuilder();
    }
}
