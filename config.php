<?php

use App\Doctrine\Entity\Content;
use App\Doctrine\Entity\Language;
use App\Middleware\LanguageDetectionMiddleware;
use App\Orm\ContentManagement\ContentDispatcher;
use App\Orm\ContentManagement\ContentPersistenceManager;
use App\Orm\ContentManagement\ContentProvider;
use App\Orm\Definition\DefinitionCompiler;
use App\Orm\Definition\EntityDefinitionLoader;
use App\Orm\Definition\EntityDefinitionProvider;
use App\Orm\EntityManager\EntityManager as JsonEntityManager;
use App\Orm\Factory\LayoutObjectFactory;
use App\Orm\Persistence\JsonDocumentManager;
use App\Orm\Repository\ObjectRepository;
use App\Services\LanguageDetection\Drivers\HttpHeaderDetectionDriver;
use App\Services\LanguageDetection\LanguageDetector;
use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
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
        $conn = [
            'driver'   => 'pdo_mysql',
            'user'     => 'root',
            'password' => 'babelino',
            'dbname'   => 'foo',
        ];

        $config = Setup::createAnnotationMetadataConfiguration(
            [__DIR__."/src/Doctrine/Entity"],
            true,
            null,
            null,
            false
        );

        return EntityManager::create($conn, $config);
    },

    ContentPersistenceManager::class => function (ContainerInterface $c) {
        return new ContentPersistenceManager(
            $c->get(EntityManager::class),
            $c->get('app-language'),
            $c->get('app-default-language')
        );
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

    LanguageDetectionMiddleware::class => function (Container $c) {
        /** @var \App\Doctrine\Repository\LanguageRepository $repo */
        $repo = $c->get(EntityManager::class)->getRepository(Language::class);

        return new LanguageDetectionMiddleware(
            new LanguageDetector(
                [
                    new HttpHeaderDetectionDriver($repo)
                ],
                $repo
            ),
            $c,
            $repo
        );
    },

    ContentProvider::class => function (Container $c) {
        /** @var \App\Doctrine\Repository\ContentRepository $repo */
        $repo = $c->get(EntityManager::class)->getRepository(Content::class);

        return new ContentProvider($repo, $c->get('app-language'));
    },
];
