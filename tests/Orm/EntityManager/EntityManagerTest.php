<?php

declare(strict_types = 1);

namespace App\Tests\Orm\EntityManager;

use App\Orm\Definition\DefinitionCompiler;
use App\Orm\Definition\EntityDefinitionLoader;
use App\Orm\Definition\EntityDefinitionProvider;
use App\Orm\Entity\Hash;
use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\JsonDocumentManager;
use App\Orm\Persistence\ReferenceAwareEntityCollection;
use App\Orm\Persistence\State\FetchedState;
use App\Orm\Repository\ObjectRepository;
use App\Tests\Orm\EntityCreators;
use App\Utilities\ObjectMap;
use PHPUnit\Framework\TestCase;
use App\Orm\Persistence\LayoutObject;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Finder\Finder;

class EntityManagerTest extends TestCase
{
    use EntityCreators;

    public function testFindByHash_WithExistingJsonDocument_ReturnsInstanceOfLayoutObject() : void
    {
        $repository = $this->createObjectRepositoryInstance();

        $layoutObject = $repository->find('6ec0bd7f-11c0-43da-975e-2a8ad9ebae0b');
        $expected = $this->createExpected();

        $this->assertEquals(json_encode($expected), json_encode($layoutObject));
        $this->assertEquals(
            [
                'e76f2ba5-9e84-4141-975c-af48a62d4ac1',
                '577d40b1-af02-4e1a-8575-3c8f0263e40d',
                '482247e6-1006-448f-aae7-102c3517f51e',
                'd6e4529e-b531-4ada-9f0b-7185b78ff811',
            ],
            $layoutObject->getHashes()
        );
        $this->assertEquals('6ec0bd7f-11c0-43da-975e-2a8ad9ebae0b', $layoutObject->getName());
    }

    protected function createObjectRepositoryInstance() : ObjectRepository
    {
        $stub = $this->createStub(JsonDocumentManager::class);
        $stub->method('fetchDocumentContent')
            ->willReturn([
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
            ]);

        $layoutObjectFactory = new LayoutObjectFactory(
            new EntityDefinitionProvider(
                __DIR__.'/../Definition/definitions',
                new EntityDefinitionLoader(new Finder(), new DefinitionCompiler()),
                new PhpFilesAdapter('definitions')
            )
        );

        return new ObjectRepository($stub, $layoutObjectFactory);
    }

    protected function createExpected() : LayoutObject
    {
        $layoutObject = new LayoutObject(
            '6ec0bd7f-11c0-43da-975e-2a8ad9ebae0b',
            new FetchedState()
        );

        $button = $this->createSimpleWidget();
        $button->setType('widget');
        $button->setWidgetType('button');
        $button->setHash(new Hash('d6e4529e-b531-4ada-9f0b-7185b78ff811'));

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $column */
        $column = $this->createGridEntity();
        $column->setType('column');
        $column->setHash(new Hash('482247e6-1006-448f-aae7-102c3517f51e'));
        $children1 = new ReferenceAwareEntityCollection([$button]);
        $children1->setReference($layoutObject);
        $column->setChildren($children1);

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $row */
        $row = $this->createGridEntity();
        $row->setType('row');
        $row->setHash(new Hash('577d40b1-af02-4e1a-8575-3c8f0263e40d'));
        $children2 = new ReferenceAwareEntityCollection([$column]);
        $children2->setReference($layoutObject);
        $row->setChildren($children2);

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface|\App\Orm\Entity\AbstractEntity $block */
        $block = $this->createGridEntity();
        $block->setType('block');
        $block->setHash(new Hash('e76f2ba5-9e84-4141-975c-af48a62d4ac1'));
        $children3 = new ReferenceAwareEntityCollection([$row]);
        $children3->setReference($layoutObject);
        $block->setChildren($children3);

        $tree = new ReferenceAwareEntityCollection([$block]);
        $tree->setReference($layoutObject);

        $layoutObject->setTree($tree);
        $layoutObject->setHashMap(new ObjectMap([
            'e76f2ba5-9e84-4141-975c-af48a62d4ac1' => $block,
            '577d40b1-af02-4e1a-8575-3c8f0263e40d' => $row,
            '482247e6-1006-448f-aae7-102c3517f51e' => $column,
            'd6e4529e-b531-4ada-9f0b-7185b78ff811' => $button,
        ]));

        return $layoutObject;
    }
}
