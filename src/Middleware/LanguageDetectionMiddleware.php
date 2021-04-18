<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Doctrine\Repository\LanguageRepository;
use App\Services\LanguageDetection\LanguageDetector;
use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LanguageDetectionMiddleware implements MiddlewareInterface
{
    protected LanguageDetector $detector;

    protected Container $container;

    protected LanguageRepository $repository;

    public function __construct(LanguageDetector $detector, Container $container, LanguageRepository $repository)
    {
        $this->detector = $detector;
        $this->container = $container;
        $this->repository = $repository;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $currentLanguage = $this->detector->detect($request);

        $this->container->set('app-language', $currentLanguage);
        $this->container->set('app-default-language', $this->repository->findDefault());

        return $handler->handle($request);
    }
}
