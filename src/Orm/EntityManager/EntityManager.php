<?php

namespace App\Orm\EntityManager;

use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\JsonDocumentFinder;
use App\Orm\Persistence\LayoutObject;

class EntityManager
{
    /**
     * @var \App\Orm\Persistence\JsonDocumentFinder
     */
    protected $finder;

    /**
     * @var \App\Orm\Factory\LayoutObjectFactory
     */
    protected $factory;

    public function __construct(JsonDocumentFinder $finder, LayoutObjectFactory $factory)
    {
        $this->finder = $finder;
        $this->factory = $factory;
    }

    /**
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