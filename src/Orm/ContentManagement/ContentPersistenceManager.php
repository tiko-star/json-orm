<?php

declare(strict_types = 1);

namespace App\Orm\ContentManagement;

use App\Doctrine\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use App\Orm\Persistence\ContentObjectStorage;

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

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $this->persistNew($dispatchedContent->getNew());
        $this->persistModified($dispatchedContent->getModified());
        $this->persistRemoved($dispatchedContent->getRemoved());
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
            $this->entityManager->persist($contentObjectStorage->getInfo());
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
        $existing = $repository->findByHashes($array->getKeys());

        while ($existing->valid()) {
            /** @var Content $existingContent */
            $existingContent = $existing->getInfo();
            /** @var \App\Orm\Entity\Hash $hash */
            $hash = $existing->current();

            /** @var Content $upcomingContent */
            $upcomingContent = $array->get((string) $hash);

            if (null !== $upcomingContent) {
                $existingContent->setContent($upcomingContent->getContent());
            }

            $existing->next();
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
