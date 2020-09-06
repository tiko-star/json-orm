<?php

declare(strict_types = 1);

namespace App\Orm\EntityManager;

use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\JsonDocumentFinder;
use App\Orm\Persistence\LayoutObject;

class EntityManager
{
    /**
     * @var \App\Orm\Persistence\JsonDocumentFinder Reference on JsonDocumentFinder instance.
     */
    protected JsonDocumentFinder $finder;

    /**
     * @var \App\Orm\Factory\LayoutObjectFactory Reference on LayoutObjectFactory instance.
     */
    protected LayoutObjectFactory $factory;

    public function __construct(JsonDocumentFinder $finder, LayoutObjectFactory $factory)
    {
        $this->finder = $finder;
        $this->factory = $factory;
    }

    /**
     * Find instance of LayoutObject by given hash.
     *
     * @param string $hash
     *
     * @return \App\Orm\Persistence\LayoutObject
     * @throws \JsonException
     */
    public function findByHash(string $hash) : LayoutObject
    {
        $content = $this->finder->fetchDocumentContent($hash);

        return $this->factory->createLayoutObject($content);
    }
}