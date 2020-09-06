<?php

namespace App\Tests\Orm\Factory;

use App\Orm\Entity\Block;
use App\Orm\Entity\BlockGroup;
use App\Orm\Entity\Button;
use App\Orm\Entity\Column;
use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\LayoutObject;
use App\Orm\Persistence\ReferenceAwareEntityCollection;
use PHPUnit\Framework\TestCase;

class LayoutObjectFactoryTest extends TestCase
{
    /**
     * @dataProvider documentProvider
     *
     * @param array                             $document
     * @param array                             $hashes
     * @param \App\Orm\Persistence\LayoutObject $expected
     */
    public function testCreateLayoutObject_WithValidDataset_CreatesValidLayoutObjects(array $document, array $hashes, LayoutObject $expected) : void
    {
        $factory = new LayoutObjectFactory();

        $layoutObject = $factory->createLayoutObject($document);

        $this->assertEquals($expected, $layoutObject);
        $this->assertEquals($hashes, $layoutObject->getHashes());
    }

    public function documentProvider()
    {
        return [
            [
                $this->createFirstDocument(),
                ['aaa', 'bbb', 'ccc', 'ddd'],
                $this->createFirstLayoutObject(),
            ],
            [
                $this->createSecondDocument(),
                ['aaa', 'bbb', 'ccc', 'ddd', 'eee', 'aaa', 'bbb', 'ccc', 'ddd', 'eee'],
                $this->createSecondLayoutObject(),
            ]
        ];
    }

    protected function createFirstDocument() : array
    {
        return [
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
        ];
    }

    protected function createFirstLayoutObject() : LayoutObject
    {
        $button = new Button();
        $button->setType('widget');
        $button->setWidgetType('button');
        $button->setHash('ddd');

        $column = new Column();
        $column->setType('column');
        $column->setHash('ccc');
        $column->setChildren(new ReferenceAwareEntityCollection([$button]));

        $block = new Block();
        $block->setType('block');
        $block->setHash('bbb');
        $block->setChildren(new ReferenceAwareEntityCollection([$column]));

        $blockGroup = new BlockGroup();
        $blockGroup->setType('blockGroup');
        $blockGroup->setHash('aaa');
        $blockGroup->setChildren(new ReferenceAwareEntityCollection([$block]));

        return new LayoutObject(new ReferenceAwareEntityCollection([$blockGroup]), ['aaa', 'bbb', 'ccc', 'ddd']);
    }

    protected function createSecondDocument() : array
    {
        return [
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
                                    ],
                                    [
                                        'type'       => 'widget',
                                        'widgetType' => 'button',
                                        'hash'       => 'eee'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
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
                                    ],
                                    [
                                        'type'       => 'widget',
                                        'widgetType' => 'button',
                                        'hash'       => 'eee'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    protected function createSecondLayoutObject() : LayoutObject
    {
        $button1 = new Button();
        $button1->setType('widget');
        $button1->setWidgetType('button');
        $button1->setHash('ddd');

        $button2 = new Button();
        $button2->setType('widget');
        $button2->setWidgetType('button');
        $button2->setHash('eee');

        $column1 = new Column();
        $column1->setType('column');
        $column1->setHash('ccc');
        $column1->setChildren(new ReferenceAwareEntityCollection([$button1, $button2]));

        $block1 = new Block();
        $block1->setType('block');
        $block1->setHash('bbb');
        $block1->setChildren(new ReferenceAwareEntityCollection([$column1]));

        $blockGroup1 = new BlockGroup();
        $blockGroup1->setType('blockGroup');
        $blockGroup1->setHash('aaa');
        $blockGroup1->setChildren(new ReferenceAwareEntityCollection([$block1]));

        $button3 = new Button();
        $button3->setType('widget');
        $button3->setWidgetType('button');
        $button3->setHash('ddd');

        $button4 = new Button();
        $button4->setType('widget');
        $button4->setWidgetType('button');
        $button4->setHash('eee');

        $column2 = new Column();
        $column2->setType('column');
        $column2->setHash('ccc');
        $column2->setChildren(new ReferenceAwareEntityCollection([$button3, $button4]));

        $block2 = new Block();
        $block2->setType('block');
        $block2->setHash('bbb');
        $block2->setChildren(new ReferenceAwareEntityCollection([$column2]));

        $blockGroup2 = new BlockGroup();
        $blockGroup2->setType('blockGroup');
        $blockGroup2->setHash('aaa');
        $blockGroup2->setChildren(new ReferenceAwareEntityCollection([$block2]));

        return new LayoutObject(
            new ReferenceAwareEntityCollection([$blockGroup1, $blockGroup2]),
            ['aaa', 'bbb', 'ccc', 'ddd', 'eee', 'aaa', 'bbb', 'ccc', 'ddd', 'eee']
        );
    }

    protected function createFactoryInstance() : LayoutObjectFactory
    {
        return new LayoutObjectFactory();
    }
}
