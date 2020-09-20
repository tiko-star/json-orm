<?php

use App\Orm\Repository\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
use App\Orm\Persistence\JsonDocumentManager;
use App\Orm\EntityManager as JsonEntityManager;
use App\Orm\Factory\LayoutObjectFactory;

return [
    JsonDocumentManager::class => DI\create(JsonDocumentManager::class)->constructor(__DIR__),

    JsonEntityManager::class => function (ContainerInterface $c) {
        return new JsonEntityManager(
            $c->get(JsonDocumentManager::class),
            $c->get(LayoutObjectFactory::class),
        );
    },

    ObjectRepository::class => function (ContainerInterface $c) {
        return new ObjectRepository(
            $c->get(JsonDocumentManager::class),
            $c->get(LayoutObjectFactory::class)
        );
    },

    EntityManager::class => function () {
        $params = [
            'driver'   => 'pdo_mysql',
            'user'     => 'root',
            'password' => 'babelino',
            'dbname'   => 'foo',
        ];

        $paths = [__DIR__.'/src/Doctrine/Entity'];
        $proxyDir = __DIR__.'/src/Doctrine/Proxy';

        $config = Setup::createAnnotationMetadataConfiguration($paths, false, $proxyDir, null, false);

        return EntityManager::create($params, $config);
    },
];