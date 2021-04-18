<?php

declare(strict_types = 1);

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Content;
use App\Doctrine\Entity\Language;
use App\Orm\Entity\Hash;
use App\Orm\Persistence\ContentObjectStorage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class ContentRepository extends EntityRepository
{
    public function findByHashes(array $hashes) : ContentObjectStorage
    {
        $contents = $this->createQueryBuilder('c')
            ->where('c.hash IN (:hashes)')
            ->setParameter('hashes', $hashes)
            ->getQuery()
            ->getResult();

        $storage = new ContentObjectStorage();

        /** @var Content $content */
        foreach ($contents as $content) {
            $storage->attach(new Hash($content->getHash()), $content);
        }

        return $storage;
    }

    public function findByHashesAndLanguage(array $hashes, Language $language, bool $includeFallback = false) : ArrayCollection
    {
        $builder = $this->createQueryBuilder('c')
            ->where('c.hash IN (:hashes)')
            ->andWhere('c.languageId = :lang_id')
            ->setParameter('hashes', $hashes)
            ->setParameter('lang_id', $language->getId());

        if ($includeFallback) {
            $builder->orWhere('c.languageId IS NULL');
        }

        $contents = $builder
            ->getQuery()
            ->getResult();

        return new ArrayCollection($contents);
    }
}
