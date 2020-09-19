<?php

declare(strict_types = 1);

namespace App\Orm\EntityManager;

use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\JsonDocumentManager;
use App\Orm\Persistence\LayoutObject;

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
     * Find instance of LayoutObject by given hash.
     *
     * @param string $hash
     *
     * @return \App\Orm\Persistence\LayoutObject
     * @throws \Exception
     */
    public function findByHash(string $hash) : LayoutObject
    {
        $content = $this->documentManager->fetchDocumentContent($hash);

        return $this->factory->createLayoutObject($content, $hash);
    }

    /**
     * @param array       $data
     *
     * @return \App\Orm\Persistence\LayoutObject
     * @throws \Exception
     */
    public function persist(array $data) : LayoutObject
    {
        $layoutObject = $this->factory->createLayoutObject($data);
        $json = json_encode($layoutObject, JSON_PRETTY_PRINT);

        $this->documentManager->save($layoutObject->getName(), $json);

        return $layoutObject;
    }
}