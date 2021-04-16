<?php

declare(strict_types = 1);

namespace App\Services\LanguageDetection\Drivers;

use App\Doctrine\Entity\Language;
use App\Doctrine\Repository\LanguageRepository;
use App\Services\LanguageDetection\LanguageDetectionDriver;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Language detection driver for detecting language from specified HTTP header.
 *
 * @package App\Services\LanguageDetection\Drivers
 */
class HttpHeaderDetectionDriver implements LanguageDetectionDriver
{
    protected LanguageRepository $repository;

    public function __construct(LanguageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Detect whether language detection is supported by current driver.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return bool
     */
    public function supports(ServerRequestInterface $request) : bool
    {
        return $request->hasHeader('x-current-language-code');
    }

    /**
     * Return the detected language model by driver.
     * Return null, if driver was unable to detect the language.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \App\Doctrine\Entity\Language|null
     */
    public function detect(ServerRequestInterface $request) : ?Language
    {
        return $this->repository->findByCode($request->getHeaderLine('x-current-language-code'));
    }
}
