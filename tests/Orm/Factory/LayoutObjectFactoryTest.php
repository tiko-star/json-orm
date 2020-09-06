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

    public function documentProvider() : array
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
        $layoutObject = new LayoutObject();

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
        $children1 = new ReferenceAwareEntityCollection([$button1, $button2]);
        $children1->setReference($layoutObject);
        $column1->setChildren($children1);

        $block1 = new Block();
        $block1->setType('block');
        $block1->setHash('bbb');
        $children2 = new ReferenceAwareEntityCollection([$column1]);
        $children2->setReference($layoutObject);
        $block1->setChildren($children2);

        $blockGroup1 = new BlockGroup();
        $blockGroup1->setType('blockGroup');
        $blockGroup1->setHash('aaa');
        $children3 = new ReferenceAwareEntityCollection([$block1]);
        $children3->setReference($layoutObject);
        $blockGroup1->setChildren($children3);

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
        $children4 = new ReferenceAwareEntityCollection([$button3, $button4]);
        $children4->setReference($layoutObject);
        $column2->setChildren($children4);

        $block2 = new Block();
        $block2->setType('block');
        $block2->setHash('bbb');
        $children5 = new ReferenceAwareEntityCollection([$column2]);
        $children5->setReference($layoutObject);
        $block2->setChildren($children5);

        $blockGroup2 = new BlockGroup();
        $blockGroup2->setType('blockGroup');
        $blockGroup2->setHash('aaa');
        $children6 = new ReferenceAwareEntityCollection([$block2]);
        $children6->setReference($layoutObject);
        $blockGroup2->setChildren($children6);

        $tree = new ReferenceAwareEntityCollection([$blockGroup1, $blockGroup2]);
        $tree->setReference($layoutObject);

        $layoutObject->setTree($tree);
        $layoutObject->setHashes(['aaa', 'bbb', 'ccc', 'ddd', 'eee', 'aaa', 'bbb', 'ccc', 'ddd', 'eee']);

        return $layoutObject;
    }

    protected function createFactoryInstance() : LayoutObjectFactory
    {
        return new LayoutObjectFactory();
    }
}
