<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Factory;

use App\Orm\Definition\DefinitionCompiler;
use App\Orm\Definition\EntityDefinitionLoader;
use App\Orm\Definition\EntityDefinitionProvider;
use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\LayoutObject;
use App\Orm\Persistence\ReferenceAwareEntityCollection;
use App\Tests\Orm\EntityCreators;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Finder\Finder;

class LayoutObjectFactoryTest extends TestCase
{
    use EntityCreators;

    /**
     * @dataProvider documentProvider
     *
     * @param array                             $document
     * @param array                             $hashes
     * @param \App\Orm\Persistence\LayoutObject $expected
     *
     * @throws \Exception
     */
    public function testCreateLayoutObject_WithValidDataset_CreatesValidLayoutObjects(array $document, array $hashes, LayoutObject $expected) : void
    {
        $factory = $this->createFactoryInstance();

        $layoutObject = $factory->createLayoutObject($document);

        $this->assertEquals(json_encode($expected), json_encode($layoutObject));
        $this->assertEquals($hashes, $layoutObject->getHashes());
    }

    protected function createFactoryInstance() : LayoutObjectFactory
    {
        return new LayoutObjectFactory(
            new EntityDefinitionProvider(
                __DIR__.'/../Definition/definitions',
                new EntityDefinitionLoader(new Finder(), new DefinitionCompiler()),
                new PhpFilesAdapter('definitions')
            )
        );
    }

    public function documentProvider() : array
    {
        return [
            [
                $this->createFirstDocument(),
                [
                    'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
                    '577d40b1-af02-4e1a-8575-3c8f0263e40d',
                    '482247e6-1006-448f-aae7-102c3517f51e',
                    'd6e4529e-b531-4ada-9f0b-7185b78ff811',
                ],
                $this->createFirstLayoutObject(),
            ],
            [
                $this->createSecondDocument(),
                [
                    'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
                    '577d40b1-af02-4e1a-8575-3c8f0263e40d',
                    '482247e6-1006-448f-aae7-102c3517f51e',
                    'd6e4529e-b531-4ada-9f0b-7185b78ff811',
                    '7d2cba14-c8d2-42d8-a81a-c169f88713c4',
                    'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
                    '577d40b1-af02-4e1a-8575-3c8f0263e40d',
                    '482247e6-1006-448f-aae7-102c3517f51e',
                    'd6e4529e-b531-4ada-9f0b-7185b78ff811',
                    '7d2cba14-c8d2-42d8-a81a-c169f88713c4',
                ],
                $this->createSecondLayoutObject(),
            ]
        ];
    }

    protected function createFirstDocument() : array
    {
        return [
            [
                'type'     => 'block',
                'hash'     => 'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
                'children' => [
                    [
                        'type'     => 'row',
                        'hash'     => '577d40b1-af02-4e1a-8575-3c8f0263e40d',
                        'children' => [
                            [
                                'type'     => 'column',
                                'hash'     => '482247e6-1006-448f-aae7-102c3517f51e',
                                'children' => [
                                    [
                                        'type'       => 'widget',
                                        'widgetType' => 'button',
                                        'hash'       => 'd6e4529e-b531-4ada-9f0b-7185b78ff811'
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

        $button = $this->createSimpleWidget();
        $button->setType('widget');
        $button->setWidgetType('button');
        $button->setHash('d6e4529e-b531-4ada-9f0b-7185b78ff811');

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $column */
        $column = $this->createGridEntity();
        $column->setType('column');
        $column->setHash('482247e6-1006-448f-aae7-102c3517f51e');
        $children1 = new ReferenceAwareEntityCollection([$button]);
        $children1->setReference($layoutObject);
        $column->setChildren($children1);

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $row */
        $row = $this->createGridEntity();
        $row->setType('row');
        $row->setHash('577d40b1-af02-4e1a-8575-3c8f0263e40d');
        $children2 = new ReferenceAwareEntityCollection([$column]);
        $children2->setReference($layoutObject);
        $row->setChildren($children2);

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $block */
        $block = $this->createGridEntity();
        $block->setType('block');
        $block->setHash('e76f2ba5-9e84-4141-975c-af48a62d4ac1');
        $children3 = new ReferenceAwareEntityCollection([$row]);
        $children3->setReference($layoutObject);
        $block->setChildren($children3);

        $tree = new ReferenceAwareEntityCollection([$block]);
        $tree->setReference($layoutObject);

        $layoutObject->setTree($tree);
        $layoutObject->setHashes([
            'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
            '577d40b1-af02-4e1a-8575-3c8f0263e40d',
            '482247e6-1006-448f-aae7-102c3517f51e',
            'd6e4529e-b531-4ada-9f0b-7185b78ff811',
        ]);

        return $layoutObject;
    }

    protected function createSecondDocument() : array
    {
        return [
            [
                'type'     => 'block',
                'hash'     => 'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
                'children' => [
                    [
                        'type'     => 'row',
                        'hash'     => '577d40b1-af02-4e1a-8575-3c8f0263e40d',
                        'children' => [
                            [
                                'type'     => 'column',
                                'hash'     => '482247e6-1006-448f-aae7-102c3517f51e',
                                'children' => [
                                    [
                                        'type'       => 'widget',
                                        'widgetType' => 'button',
                                        'hash'       => 'd6e4529e-b531-4ada-9f0b-7185b78ff811'
                                    ],
                                    [
                                        'type'       => 'widget',
                                        'widgetType' => 'button',
                                        'hash'       => '7d2cba14-c8d2-42d8-a81a-c169f88713c4'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type'     => 'block',
                'hash'     => 'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
                'children' => [
                    [
                        'type'     => 'row',
                        'hash'     => '577d40b1-af02-4e1a-8575-3c8f0263e40d',
                        'children' => [
                            [
                                'type'     => 'column',
                                'hash'     => '482247e6-1006-448f-aae7-102c3517f51e',
                                'children' => [
                                    [
                                        'type'       => 'widget',
                                        'widgetType' => 'button',
                                        'hash'       => 'd6e4529e-b531-4ada-9f0b-7185b78ff811'
                                    ],
                                    [
                                        'type'       => 'widget',
                                        'widgetType' => 'button',
                                        'hash'       => '7d2cba14-c8d2-42d8-a81a-c169f88713c4'
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

        $button1 = $this->createSimpleWidget();
        $button1->setType('widget');
        $button1->setWidgetType('button');
        $button1->setHash('d6e4529e-b531-4ada-9f0b-7185b78ff811');

        $button2 = $this->createSimpleWidget();
        $button2->setType('widget');
        $button2->setWidgetType('button');
        $button2->setHash('7d2cba14-c8d2-42d8-a81a-c169f88713c4');

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $column1 */
        $column1 = $this->createGridEntity();
        $column1->setType('column');
        $column1->setHash('482247e6-1006-448f-aae7-102c3517f51e');
        $children1 = new ReferenceAwareEntityCollection([$button1, $button2]);
        $children1->setReference($layoutObject);
        $column1->setChildren($children1);

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $row1 */
        $row1 = $this->createGridEntity();
        $row1->setType('row');
        $row1->setHash('577d40b1-af02-4e1a-8575-3c8f0263e40d');
        $children2 = new ReferenceAwareEntityCollection([$column1]);
        $children2->setReference($layoutObject);
        $row1->setChildren($children2);

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $block1 */
        $block1 = $this->createGridEntity();
        $block1->setType('block');
        $block1->setHash('e76f2ba5-9e84-4141-975c-af48a62d4ac1');
        $children3 = new ReferenceAwareEntityCollection([$row1]);
        $children3->setReference($layoutObject);
        $block1->setChildren($children3);

        $button3 = $this->createSimpleWidget();
        $button3->setType('widget');
        $button3->setWidgetType('button');
        $button3->setHash('d6e4529e-b531-4ada-9f0b-7185b78ff811');

        $button4 = $this->createSimpleWidget();
        $button4->setType('widget');
        $button4->setWidgetType('button');
        $button4->setHash('7d2cba14-c8d2-42d8-a81a-c169f88713c4');

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $column2 */
        $column2 = $this->createGridEntity();
        $column2->setType('column');
        $column2->setHash('482247e6-1006-448f-aae7-102c3517f51e');
        $children4 = new ReferenceAwareEntityCollection([$button3, $button4]);
        $children4->setReference($layoutObject);
        $column2->setChildren($children4);

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $row2 */
        $row2 = $this->createGridEntity();
        $row2->setType('row');
        $row2->setHash('577d40b1-af02-4e1a-8575-3c8f0263e40d');
        $children5 = new ReferenceAwareEntityCollection([$column2]);
        $children5->setReference($layoutObject);
        $row2->setChildren($children5);

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $block2 */
        $block2 = $this->createGridEntity();
        $block2->setType('block');
        $block2->setHash('e76f2ba5-9e84-4141-975c-af48a62d4ac1');
        $children6 = new ReferenceAwareEntityCollection([$row2]);
        $children6->setReference($layoutObject);
        $block2->setChildren($children6);

        $tree = new ReferenceAwareEntityCollection([$block1, $block2]);
        $tree->setReference($layoutObject);

        $layoutObject->setTree($tree);
        $layoutObject->setHashes([
            'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
            '577d40b1-af02-4e1a-8575-3c8f0263e40d',
            '482247e6-1006-448f-aae7-102c3517f51e',
            'd6e4529e-b531-4ada-9f0b-7185b78ff811',
            '7d2cba14-c8d2-42d8-a81a-c169f88713c4',
            'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
            '577d40b1-af02-4e1a-8575-3c8f0263e40d',
            '482247e6-1006-448f-aae7-102c3517f51e',
            'd6e4529e-b531-4ada-9f0b-7185b78ff811',
            '7d2cba14-c8d2-42d8-a81a-c169f88713c4',
        ]);

        return $layoutObject;
    }
}
