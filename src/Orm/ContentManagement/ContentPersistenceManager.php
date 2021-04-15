<?php

declare(strict_types = 1);

namespace App\Orm\ContentManagement;

use App\Doctrine\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use App\Orm\Persistence\ContentObjectStorage;

class ContentPersistenceManager
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function persist(DispatchedContent $dispatchedContent) : void
    {
        $this->persistNew($dispatchedContent->getNew());
        $this->persistModified($dispatchedContent->getModified());
        $this->persistRemoved($dispatchedContent->getRemoved());
    }

    public function persistNew(ContentObjectStorage $contentObjectStorage) : void
    {
        $contentObjectStorage->rewind();

        while ($contentObjectStorage->valid()) {
            $this->entityManager->persist($contentObjectStorage->getInfo());
            $contentObjectStorage->next();
        }

        $this->entityManager->flush();

    }

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
