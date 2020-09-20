<?php

declare(strict_types = 1);

use App\Orm\Repository\ObjectRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use App\Orm\EntityManager\EntityManager as JsonEntityManager;
use App\Orm\Factory\LayoutObjectFactory;

use Doctrine\ORM\EntityManager;
use App\Doctrine\Entity\Content;

require __DIR__.'/vendor/autoload.php';

$builder = new DI\ContainerBuilder();
$builder->enableCompilation(__DIR__.'/tmp');
$builder->writeProxiesToFile(true, __DIR__.'/tmp/proxies');
$builder->addDefinitions(__DIR__ . '/config.php');

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

// Add Routing Middleware
$app->addRoutingMiddleware();

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
$app->get('/layout/{filename}', function (Request $request, Response $response, $args) {
    /** @var ObjectRepository $objectRepository */
    $objectRepository = $this->get(ObjectRepository::class);

    /** @var \App\Doctrine\Repository\ContentRepository $contentRepository */
    $contentRepository = $this->get(EntityManager::class)->getRepository(Content::class);

    $layout = $objectRepository->find($args['filename']);
    $contents = $contentRepository->findByHashes($layout->getHashes());

    $layout->setContents($contents);

    $response->getBody()->write(json_encode($layout));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/layout', function (Request $request, Response $response) {
    $content = $request->getParsedBody();

    /** @var LayoutObjectFactory $factory */
    $factory = $this->get(LayoutObjectFactory::class);
    $layoutObject = $factory->createLayoutObject($content, md5((string) time()));

    /** @var JsonEntityManager $jsonEntityManager */
    $jsonEntityManager = $this->get(JsonEntityManager::class);
    $jsonEntityManager->persist($layoutObject);

    $response->getBody()->write(json_encode($layoutObject));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/content/{hash}', function (Request $request, Response $response) {
    $content = new Content();

    $content->setHash(md5((string) time()));
    $content->setContent($request->getParsedBody());

    /** @var EntityManager $manager */
    $manager = $this->get(EntityManager::class);
    $manager->persist($content);
    $manager->flush();

    $response->getBody()->write(json_encode($content));

    return $response->withHeader('Content-Type', 'application/json');
});

// Run app
$app->run();