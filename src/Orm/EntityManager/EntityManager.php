<?php

declare(strict_types = 1);

namespace App\Orm\EntityManager;

use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\JsonDocumentManager;
use App\Orm\Persistence\LayoutObject;
use App\Orm\Persistence\State\PersistingState;

class EntityManager
{
    /**
     * @var \App\Orm\Persistence\JsonDocumentManager Reference on JsonDocumentManager instance.
     */
    protected JsonDocumentManager $documentManager;

    /**
     * @var \App\Orm\Factory\LayoutObjectFactory Reference on LayoutObjectFactory instance.
     */
    protected LayoutObjectFactory $factory;

    public function __construct(JsonDocumentManager $documentManager, LayoutObjectFactory $factory)
    {
        $this->documentManager = $documentManager;
        $this->factory = $factory;
    }

    /**
     * @param array $data
     *
     * @return \App\Orm\Persistence\LayoutObject
     * @throws \Exception
     */
    public function persist(array $data) : LayoutObject
    {
        $layoutObject = $this->factory->createLayoutObject($data, md5((string) time()));
        $layoutObject->setState(new PersistingState());

        $json = json_encode($layoutObject, JSON_PRETTY_PRINT);

        $this->documentManager->save($layoutObject->getName(), $json);

        return $layoutObject;
    }
}