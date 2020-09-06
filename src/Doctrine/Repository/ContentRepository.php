<?php

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class ContentRepository extends EntityRepository
{
    public function findByHashes(array $hashes) : ArrayCollection
    {
        $contents = $this->_em->createQueryBuilder()
            ->select('c')
            ->from(Content::class, 'c')
            ->where('c.hash IN (:hashes)')
            ->setParameter('hashes', $hashes)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($contents);
    }
}