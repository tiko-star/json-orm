<?php

declare(strict_types = 1);

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class ContentRepository extends EntityRepository
{
    public function findAllByHashes(array $hashes) : ArrayCollection
    {
        $contents = $this->createQueryBuilder('c')
            ->where('c.hash IN (:hashes)')
            ->setParameter('hashes', $hashes)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($contents);
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
