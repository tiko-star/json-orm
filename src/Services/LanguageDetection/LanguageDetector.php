<?php

declare(strict_types = 1);

namespace App\Services\LanguageDetection;

use App\Doctrine\Entity\Language;
use App\Doctrine\Repository\LanguageRepository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Service for detecting application language for the current HTTP request.
 *
 * @package App\Services\LanguageDetection
 */
class LanguageDetector
{
    /**
     * @var \App\Services\LanguageDetection\LanguageDetectionDriver[] List of registered detection drivers.
     */
    protected array $drivers;

    /**
     * @var \App\Doctrine\Repository\LanguageRepository
     */
    protected LanguageRepository $repository;

    /**
     * LanguageDetector constructor.
     *
     * @param array                                       $drivers
     * @param \App\Doctrine\Repository\LanguageRepository $repository
     */
    public function __construct(array $drivers, LanguageRepository $repository)
    {
        $this->drivers = $drivers;
        $this->repository = $repository;
    }


    /**
     * Detect language.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \App\Doctrine\Entity\Language
     */
    public function detect(ServerRequestInterface $request) : Language
    {
        $language = null;

        foreach ($this->drivers as $driver) {
            if ($driver->supports($request)) {
                $language = $driver->detect($request);
                break;
            }
        }

        return $language ?? $this->repository->findDefault();
    }
}
