<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Persistence;

use App\Orm\Definition\DefinitionCompiler;
use App\Orm\Definition\EntityDefinitionLoader;
use App\Orm\Definition\EntityDefinitionProvider;
use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Widget;
use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\LayoutObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Finder\Finder;

class LayoutObjectTest extends TestCase
{
    protected string $json = <<<JSON
[
    {
        "type": "block",
        "hash": "e634268e-918e-11eb-9578-ada57c2a135e",
        "children": [
            {
                "type": "row",
                "hash": "e63427ba-918e-11eb-9cea-ada57c2a135e",
                "children": [
                    {
                        "type": "column",
                        "hash": "e634280a-918e-11eb-b2a0-ada57c2a135e",
                        "children": [
                            {
                                "type": "widget",
                                "hash": "e6342850-918e-11eb-9bba-ada57c2a135e",
                                "props": {
                                    "text": "Sign In"
                                },
                                "widgetType": "button"
                            },
                            {
                                "type": "widget",
                                "hash": "e6342882-918e-11eb-8e4a-ada57c2a135e",
                                "props": {
                                    "text": "Register"
                                },
                                "widgetType": "button"
                            }
                        ]
                    }
                ]
            }
        ]
    }
]
JSON;

    public function testFindEntityByHash_WithExistingHash_ReturnsEntityInstance() : void
    {
        $layoutObject = $this->createLayoutObject(json_decode($this->json, true));

        /** @var Widget $entity */
        $entity = $layoutObject->findEntityByHash('e6342850-918e-11eb-9bba-ada57c2a135e');

        $this->assertInstanceOf(AbstractEntity::class, $entity);
        $this->assertInstanceOf(Widget::class, $entity);
        $this->assertEquals('widget', $entity->getType());
        $this->assertEquals('button', $entity->getWidgetType());
        $this->assertEquals('e6342850-918e-11eb-9bba-ada57c2a135e', (string) $entity->getHash());
    }

    public function testFindEntityByHash_WithWrongHash_ReturnsNull() : void
    {
        $layoutObject = $this->createLayoutObject(json_decode($this->json, true));

        $this->assertNull($layoutObject->findEntityByHash('e6342850-918e-11eb-9bba-ada57c2a135e-xxx'));
    }

    /**
     * @param array $document
     *
     * @return \App\Orm\Persistence\LayoutObject
     * @throws \App\Orm\Exception\InvalidEntityHashException
     * @throws \App\Orm\Exception\InvalidEntityTypeException
     * @throws \App\Orm\Exception\MissingEntityTypeIdentifierException
     */
    protected function createLayoutObject(array $document) : LayoutObject
    {
        $factory = new LayoutObjectFactory(
            new EntityDefinitionProvider(
                __DIR__.'/../Definition/definitions',
                new EntityDefinitionLoader(new Finder(), new DefinitionCompiler()),
                $this->createCacheMock()
            )
        );

        return $factory->createLayoutObject($document);
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
