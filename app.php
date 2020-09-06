<?php

declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;

use App\Orm\EntityManager\EntityManager as JsonEntityManager;
use App\Orm\Persistence\JsonDocumentFinder;
use App\Orm\Factory\LayoutObjectFactory;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use App\Doctrine\Entity\Content;

require __DIR__.'/vendor/autoload.php';

// Create Container using PHP-DI
$container = new Container();

$container->set('JsonEntityManager', function () {
    return new JsonEntityManager(
        new JsonDocumentFinder(__DIR__),
        new LayoutObjectFactory()
    );
});

$container->set('DoctrineEntityManager', function () {
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
});

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
AppFactory::setContainer($container);
$app = AppFactory::create();

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
    /** @var JsonEntityManager $manager */
    $manager = $this->get('JsonEntityManager');

    $layout = $manager->findByHash($args['filename']);

    $contents = $this->get('DoctrineEntityManager')
        ->getRepository(Content::class)
        ->findByHashes($layout->getHashes());

    $layout->setContents($contents);

    $response->getBody()->write(json_encode($layout));

    return $response->withHeader('Content-Type', 'application/json');;
});

// Run app
$app->run();