<?php

declare(strict_types = 1);

namespace App\Orm\Repository;

use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\JsonDocumentManager;
use App\Orm\Persistence\LayoutObject;
use App\Orm\Persistence\State\FetchedState;

class ObjectRepository
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
     * Find instance of LayoutObject by given name.
     *
     * @param string $documentName
     *
     * @return \App\Orm\Persistence\LayoutObject
     */
    public function find(string $documentName) : LayoutObject
    {
        $content = $this->documentManager->fetchDocumentContent($documentName);

        return $this->factory->createLayoutObject($content, $documentName, new FetchedState());
    }
}