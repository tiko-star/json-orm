<?php

namespace App\Tests\Orm\EntityManager;

use App\Orm\Entity\Block;
use App\Orm\Entity\BlockGroup;
use App\Orm\Entity\Button;
use App\Orm\Entity\Column;
use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\JsonDocumentFinder;
use App\Orm\Persistence\ReferenceAwareEntityCollection;
use PHPUnit\Framework\TestCase;

use App\Orm\EntityManager\EntityManager;
use App\Orm\Persistence\LayoutObject;

class EntityManagerTest extends TestCase
{
    public function testFindByHash_WithExistingJsonDocument_ReturnsInstanceOfLayoutObject() : void
    {
        $manager = $this->createEntityManager();

        $layoutObject = $manager->findByHash('6ec0bd7f-11c0-43da-975e-2a8ad9ebae0b');
        $expected = $this->createExpected();

        $this->assertEquals($expected, $layoutObject);
        $this->assertEquals(['aaa', 'bbb', 'ccc', 'ddd'], $layoutObject->getHashes());
    }

    protected function createEntityManager() : EntityManager
    {
        $stub = $this->createStub(JsonDocumentFinder::class);
        $stub->method('fetchDocumentContent')
            ->willReturn([
                [
                    'type'     => 'blockGroup',
                    'hash'     => 'aaa',
                    'children' => [
                        [
                            'type'     => 'block',
                            'hash'     => 'bbb',
                            'children' => [
                                [
                                    'type'     => 'column',
                                    'hash'     => 'ccc',
                                    'children' => [
                                        [
                                            'type'       => 'widget',
                                            'widgetType' => 'button',
                                            'hash'       => 'ddd'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ]);

        return new EntityManager($stub, new LayoutObjectFactory());
    }

    protected function createExpected() : LayoutObject
    {
        $layoutObject = new LayoutObject();

        $button = new Button();
        $button->setType('widget');
        $button->setWidgetType('button');
        $button->setHash('ddd');

        $column = new Column();
        $column->setType('column');
        $column->setHash('ccc');
        $children1 = new ReferenceAwareEntityCollection([$button]);
        $children1->setReference($layoutObject);
        $column->setChildren($children1);

        $block = new Block();
        $block->setType('block');
        $block->setHash('bbb');
        $children2 = new ReferenceAwareEntityCollection([$column]);
        $children2->setReference($layoutObject);
        $block->setChildren($children2);

        $blockGroup = new BlockGroup();
        $blockGroup->setType('blockGroup');
        $blockGroup->setHash('aaa');
        $children3 = new ReferenceAwareEntityCollection([$block]);
        $children3->setReference($layoutObject);
        $blockGroup->setChildren($children3);

        $tree = new ReferenceAwareEntityCollection([$blockGroup]);
        $tree->setReference($layoutObject);

        $layoutObject->setTree($tree);
        $layoutObject->setHashes(['aaa', 'bbb', 'ccc', 'ddd']);

        return $layoutObject;
    }
}