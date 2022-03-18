<?php

declare(strict_types = 1);

use App\Doctrine\Entity\Language;
use App\Middleware\LanguageDetectionMiddleware;
use App\Orm\ContentManagement\ContentDispatcher;
use App\Orm\ContentManagement\ContentPersistenceManager;
use App\Orm\Repository\ObjectRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Orm\ContentManagement\ContentProvider;

use App\Orm\EntityManager\EntityManager as JsonEntityManager;
use App\Orm\Factory\LayoutObjectFactory;

use Doctrine\ORM\EntityManager;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Middleware\ContentLengthMiddleware;

require __DIR__.'/vendor/autoload.php';

$builder = new DI\ContainerBuilder();
$builder->enableCompilation(__DIR__.'/tmp');
$builder->writeProxiesToFile(true, __DIR__.'/tmp/proxies');
$builder->addDefinitions(__DIR__.'/config.php');
$builder->useAutowiring(false);
$builder->useAnnotations(false);

$container = $builder->build();

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
AppFactory::setContainer($container);
$app = AppFactory::create();

// Parse json, form data and xml
$app->addBodyParsingMiddleware();

$app->add(new ContentLengthMiddleware());

// Add Routing Middleware
$app->addRoutingMiddleware();

$app->add(LanguageDetectionMiddleware::class);

/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors           -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails     -> Display error details in error log
 *                                  which can be replaced by a callable of your choice.
 *                                  Note: This middleware should be added last. It will not handle any exceptions/errors
 *                                  for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define app routes

$app->group('/languages', function (RouteCollectorProxyInterface $proxy) {
    $proxy->get('', function (Request $request, Response $response) {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get(EntityManager::class);

        /** @var \App\Doctrine\Repository\LanguageRepository $languageRepository */
        $languageRepository = $entityManager->getRepository(Language::class);
        $languages = $languageRepository->findAll();

        $response->getBody()->write(json_encode($languages));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $proxy->post('', function (Request $request, Response $response) {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get(EntityManager::class);
        $content = $request->getParsedBody();

        $language = new Language();

        $language->setName($content['name']);
        $language->setCode($content['code']);
        $language->setIsDefault($content['isDefault']);

        $entityManager->persist($language);
        $entityManager->flush();

        $response->getBody()->write(json_encode($language));

        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    });

    $proxy->patch('/{id}', function (Request $request, Response $response, array $args) {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get(EntityManager::class);

        /** @var \Doctrine\ORM\EntityRepository $languageRepository */
        $languageRepository = $entityManager->getRepository(Language::class);

        /** @var Language $language */
        $language = $languageRepository->find($args['id']);

        if (null === $language) {
            $response->getBody()->write(json_encode(['message' => 'Not found!']));

            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $content = $request->getParsedBody();
        $language->setName($content['name']);
        $language->setCode($content['code']);
        $language->setIsDefault($content['isDefault']);

        $entityManager->flush();

        $response->getBody()->write(json_encode($language));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $proxy->delete('/{id}', function (Request $request, Response $response, array $args) {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get(EntityManager::class);

        /** @var \Doctrine\ORM\EntityRepository $languageRepository */
        $languageRepository = $entityManager->getRepository(Language::class);

        /** @var Language $language */
        $language = $languageRepository->find($args['id']);

        if (null === $language) {
            $response->getBody()->write(json_encode(['message' => 'Not found!']));

            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $entityManager->remove($language);
        $entityManager->flush();

        $response->getBody()->write('');

        return $response->withStatus(204)->withHeader('Content-Type', 'application/json');
    });
});

$app->group('/layouts', function (RouteCollectorProxyInterface $proxy) {
    $proxy->get('/{filename}', function (Request $request, Response $response, $args) {
        /** @var ContentProvider $contentProvider */
        $contentProvider = $this->get(ContentProvider::class);

        /** @var ObjectRepository $objectRepository */
        $objectRepository = $this->get(ObjectRepository::class);

        $layout = $objectRepository->find($args['filename']);
        $contents = $contentProvider->findByHashes($layout->getHashes());
        $layout->setContents($contents);

        $response->getBody()->write(json_encode($layout));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $proxy->post('', function (Request $request, Response $response) {
        $content = $request->getParsedBody();

        /** @var ContentDispatcher $dispatcher */
        $dispatcher = $this->get(ContentDispatcher::class);

        /** @var LayoutObjectFactory $factory */
        $factory = $this->get(LayoutObjectFactory::class);
        $layoutObject = $factory->createLayoutObject($content, md5((string) time()));

        /** @var ContentProvider $contentProvider */
        $contentProvider = $this->get(ContentProvider::class);
        // Fetch existing content.
        $existingContents = $contentProvider->findByHashes($layoutObject->getHashes());

        /** @var JsonEntityManager $jsonEntityManager */
        $jsonEntityManager = $this->get(JsonEntityManager::class);
        $jsonEntityManager->persist($layoutObject);

        // Fetch upcoming content
        $upcomingContents = $layoutObject->getContents();

        $dispatched = $dispatcher->dispatch($upcomingContents, $existingContents);
        // Persist dispatched content
        /** @var ContentPersistenceManager $contentPersistenceManager */
        $contentPersistenceManager = $this->get(ContentPersistenceManager::class);
        $contentPersistenceManager->persist($dispatched);

        $response->getBody()->write(json_encode([
            $layoutObject->getName() => $layoutObject
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $proxy->patch('/{hash}', function (Request $request, Response $response, array $args) {
        /** @var ObjectRepository $objectRepository */
        $objectRepository = $this->get(ObjectRepository::class);
        $layoutObject = $objectRepository->find($args['hash']);

        /** @var ContentProvider $contentProvider */
        $contentProvider = $this->get(ContentProvider::class);
        // Fetch existing content.
        $existingContents = $contentProvider->findByHashes($layoutObject->getHashes());

        $json = $request->getParsedBody();
        /** @var LayoutObjectFactory $factory */
        $factory = $this->get(LayoutObjectFactory::class);
        $layoutObject = $factory->createLayoutObject($json, $args['hash']);

        /** @var JsonEntityManager $jsonEntityManager */
        $jsonEntityManager = $this->get(JsonEntityManager::class);
        $jsonEntityManager->persist($layoutObject);

        // Fetch upcoming content
        $upcomingContents = $layoutObject->getContents();

        /** @var ContentDispatcher $dispatcher */
        $dispatcher = $this->get(ContentDispatcher::class);
        $dispatched = $dispatcher->dispatch($upcomingContents, $existingContents);

        // Persist dispatched content
        /** @var ContentPersistenceManager $contentPersistenceManager */
        $contentPersistenceManager = $this->get(ContentPersistenceManager::class);
        $contentPersistenceManager->persist($dispatched);
        $layoutObject->setContents($upcomingContents);

        $response->getBody()->write(json_encode([
            $layoutObject->getName() => $layoutObject
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    });
});

// Run app
$app->run();
