<?php

declare(strict_types = 1);

namespace App\Orm\ContentManagement;

use App\Doctrine\Entity\Language;
use App\Doctrine\Repository\ContentRepository;
use App\Orm\Entity\Hash;
use App\Orm\Persistence\ContentObjectStorage;

/**
 * Provides methods for content retrieval.
 * During retrieval process based on the language fetches an appropriate content.
 *
 * @package App\Orm\ContentManagement
 */
class ContentProvider
{
    /**
     * @var \App\Doctrine\Repository\ContentRepository Reference on instance of ContentRepository.
     */
    protected ContentRepository $repository;

    /**
     * @var \App\Doctrine\Entity\Language Reference on instance of the Language
     *                                    the application is bootstrapped with.
     */
    protected Language $currentLanguage;

    public function __construct(ContentRepository $repository, Language $currentLanguage)
    {
        $this->repository = $repository;
        $this->currentLanguage = $currentLanguage;
    }

    /**
     * Find content by given hashes for current language.
     * In case the content for the current language is missing retrieve fallback content.
     *
     * @param array $hashes
     *
     * @return \App\Orm\Persistence\ContentObjectStorage
     */
    public function findByHashes(array $hashes) : ContentObjectStorage
    {
        $storage = new ContentObjectStorage();
        $contents = $this->repository->findByHashesAndLanguage($hashes, $this->currentLanguage, true);

        /** @var \App\Doctrine\Entity\Content $content */
        foreach ($contents as $content) {
            $hash = new Hash($content->getHash());

            if (!$storage->contains($hash)) {
                $storage->attach($hash, $content);
                continue;
            }

            /** @var \App\Doctrine\Entity\Content $attached */
            $attached = $storage[$hash];

            // Replace fallback with current.
            if (null === $attached->getLanguageId()) {
                $storage->attach($hash, $content);
            }
        }

        return $storage;
    }
}
