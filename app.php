<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;

use App\Orm\EntityManager\EntityManager;
use App\Orm\Persistence\JsonDocumentFinder;
use App\Orm\Factory\LayoutObjectFactory;

require __DIR__ . '/vendor/autoload.php';

define('ROOT_DIR', __DIR__);

// Create Container using PHP-DI
$container = new Container();

$container->set('EntityManager', function () {
    return new EntityManager(
        new JsonDocumentFinder(),
        new LayoutObjectFactory()
    );
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
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.
 
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define app routes
$app->get('/layout/{filename}', function (Request $request, Response $response, $args) {
    /** @var EntityManager $manager */
    $manager = $this->get('EntityManager');

    $layout = $manager->findByHash($args['filename']);
    $payload = json_encode($layout);

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');;
});

// Run app
$app->run();