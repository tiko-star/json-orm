<?php

declare(strict_types = 1);

namespace App\Orm\ContentManagement;

use App\Doctrine\Entity\Content;
use App\Doctrine\Entity\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use App\Orm\Persistence\ContentObjectStorage;

use function count;

/**
 * Handles content persistence management.
 * Insets new content into database.
 * Updates modified content in database.
 * Removes removed content from database.
 *
 * @package App\Orm\ContentManagement
 */
class ContentPersistenceManager
{
    /**
     * @var \Doctrine\ORM\EntityManager Reference on instance of EntityManager.
     */
    protected EntityManager $entityManager;

    protected Language $currentLanguage;

    protected Language $defaultLanguage;

    public function __construct(EntityManager $entityManager, Language $currentLanguage, Language $defaultLanguage)
    {
        $this->entityManager = $entityManager;
        $this->currentLanguage = $currentLanguage;
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * Persist content into database.
     * Execute appropriate SQL operations.
     *
     * @param \App\Orm\ContentManagement\DispatchedContent $dispatchedContent
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persist(DispatchedContent $dispatchedContent) : void
    {
        if (count($dispatchedContent->getNew())) {
            $this->persistNew($dispatchedContent->getNew());
        }

        if (count($dispatchedContent->getModified())) {
            $this->persistModified($dispatchedContent->getModified());
        }

        if (count($dispatchedContent->getRemoved())) {
            $this->persistRemoved($dispatchedContent->getRemoved());
        }
    }

    /**
     * Insert new content into database.
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $contentObjectStorage
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persistNew(ContentObjectStorage $contentObjectStorage) : void
    {
        $contentObjectStorage->rewind();

        while ($contentObjectStorage->valid()) {
            /** @var Content $content */
            $content = $contentObjectStorage->getInfo();
            $content->setLanguageId($this->currentLanguage->getId());

            $fallback = clone $content;
            $fallback->setLanguageId(null);

            $this->entityManager->persist($content);
            $this->entityManager->persist($fallback);
            $contentObjectStorage->next();
        }

        $this->entityManager->flush();
    }

    /**
     * Update modified content in database.
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $contentObjectStorage
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persistModified(ContentObjectStorage $contentObjectStorage) : void
    {
        $array = new ArrayCollection();
        $contentObjectStorage->rewind();

        while ($contentObjectStorage->valid()) {
            $array->set((string) $contentObjectStorage->current(), $contentObjectStorage->getInfo());
            $contentObjectStorage->next();
        }

        /** @var \App\Doctrine\Repository\ContentRepository $repository */
        $repository = $this->entityManager->getRepository(Content::class);
        $existing = $repository->findByHashesAndLanguage(
            $array->getKeys(),
            $this->currentLanguage,
            $this->currentLanguage->isDefault()
        );

        /** @var Content $upcomingContent */
        foreach ($array as $hash => $upcomingContent) {
            /** @var Content|null $existingContent */
            $existingContent = $existing->filter(function (Content $content) use ($hash) {
                return $content->getHash() === $hash
                    && $content->getLanguageId() === $this->currentLanguage->getId();
            })->first();

            if ($this->currentLanguage->isDefault()) {
                /** @var Content $fallbackContent */
                $fallbackContent = $existing->filter(function (Content $content) use ($hash) {
                    return $content->getHash() === $hash
                        && null === $content->getLanguageId();
                })->first();

                $fallbackContent->setContent($upcomingContent->getContent());
            }

            if ($existingContent) {
                $existingContent->setContent($upcomingContent->getContent());
            } else {
                $content = new Content();
                $content->setHash($hash);
                $content->setContent($upcomingContent->getContent());
                $content->setLanguageId($this->currentLanguage->getId());
                $this->entityManager->persist($content);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Remove content from database.
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $contentObjectStorage
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persistRemoved(ContentObjectStorage $contentObjectStorage) : void
    {
        $array = new ArrayCollection();
        $contentObjectStorage->rewind();

        while ($contentObjectStorage->valid()) {
            $array->set((string) $contentObjectStorage->current(), $contentObjectStorage->getInfo());
            $contentObjectStorage->next();
        }

        /** @var \App\Doctrine\Repository\ContentRepository $repository */
        $repository = $this->entityManager->getRepository(Content::class);
        $existing = $repository->findByHashes($array->getKeys());

        while ($existing->valid()) {
            /** @var Content $existingContent */
            $existingContent = $existing->getInfo();

            $this->entityManager->remove($existingContent);
            $existing->next();
        }

        $this->entityManager->flush();
    }
}
