<?php

use App\Orm\ContentManagement\ContentDispatcher;
use App\Orm\ContentManagement\ContentPersistenceManager;
use App\Orm\Definition\DefinitionCompiler;
use App\Orm\Definition\EntityDefinitionLoader;
use App\Orm\Definition\EntityDefinitionProvider;
use App\Orm\Repository\ObjectRepository;
use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Psr\Container\ContainerInterface;
use App\Orm\Persistence\JsonDocumentManager;
use App\Orm\EntityManager\EntityManager as JsonEntityManager;
use App\Orm\Factory\LayoutObjectFactory;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Finder\Finder;

return [
    JsonDocumentManager::class => DI\create(JsonDocumentManager::class)->constructor(__DIR__),
    ContentDispatcher::class   => DI\create(ContentDispatcher::class),

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

    ContentPersistenceManager::class => function (ContainerInterface $c) {
        return new ContentPersistenceManager($c->get(EntityManager::class));
    },

    EntityDefinitionLoader::class => function (ContainerInterface $c) {
        return new EntityDefinitionLoader(new Finder(), new DefinitionCompiler());
    },

    EntityDefinitionProvider::class => function (ContainerInterface $c) {
        return new EntityDefinitionProvider(
            __DIR__.'/definitions',
            $c->get(EntityDefinitionLoader::class),
            new PhpFilesAdapter(
                'definitions',
                0,
                __DIR__.'/tmp'
            )
        );
    },

    LayoutObjectFactory::class => function (ContainerInterface $c) {
        return new LayoutObjectFactory(
            $c->get(EntityDefinitionProvider::class)
        );
    },
];
