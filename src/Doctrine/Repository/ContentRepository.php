<?php

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\Hash;
use App\Orm\Persistence\ContentObjectStorage;
use Doctrine\ORM\EntityRepository;

class ContentRepository extends EntityRepository
{
    public function findByHashes(array $hashes) : ContentObjectStorage
    {
        $contents = $this->_em->createQueryBuilder()
            ->select('c')
            ->from(Content::class, 'c')
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
}
