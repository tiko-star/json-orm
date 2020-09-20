<?php

use App\Orm\Repository\ObjectRepository;
use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Psr\Container\ContainerInterface;
use App\Orm\Persistence\JsonDocumentManager;
use App\Orm\EntityManager\EntityManager as JsonEntityManager;
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

        $cache = new PhpFileCache(__DIR__.'/src/Doctrine/Cache');
        $config = new Configuration;
        $config->setMetadataCacheImpl($cache);
        $driverImpl = $config->newDefaultAnnotationDriver(__DIR__.'/src/Doctrine/Entity');
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(__DIR__.'/src/Doctrine/Proxy');
        $config->setProxyNamespace('App\Proxies');
        $config->setAutoGenerateProxyClasses(true);

        return EntityManager::create($params, $config);
    },

    LayoutObjectFactory::class => DI\create(LayoutObjectFactory::class),
];