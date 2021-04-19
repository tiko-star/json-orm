<?php

declare(strict_types = 1);

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class ContentRepository extends EntityRepository
{
    /**
     * Find contents by given hashes.
     *
     * @param array $hashes List of entity hashes.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findByHashes(array $hashes) : ArrayCollection
    {
        $contents = $this->createQueryBuilder('c')
            ->where('c.hash IN (:hashes)')
            ->setParameter('hashes', $hashes)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($contents);
    }

    /**
     * Find contents by given hashes and given language.
     *
     * @param array                         $hashes List of entity hashes.
     * @param \App\Doctrine\Entity\Language $language Instance of particular language to search for.
     * @param bool                          $includeFallback If is set to true also retrieve fallback contents too.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
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
