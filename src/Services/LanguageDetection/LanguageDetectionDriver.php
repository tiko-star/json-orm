<?php

declare(strict_types = 1);

namespace App\Services\LanguageDetection;

use App\Doctrine\Entity\Language;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface LanguageDetectionDriver declares methods for language detection based on every HTTP request.
 *
 * @package App\Services\LanguageDetection
 */
interface LanguageDetectionDriver
{
    /**
     * Detect whether language detection is supported by current driver.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return bool
     */
    public function supports(ServerRequestInterface $request) : bool;

    /**
     * Return the detected language model by driver.
     * Return null, if driver was unable to detect the language.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \App\Doctrine\Entity\Language|null
     */
    public function detect(ServerRequestInterface $request) : ?Language;
}
