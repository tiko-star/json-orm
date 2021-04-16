<?php

declare(strict_types = 1);

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Language;
use Doctrine\ORM\EntityRepository;

/**
 * Class LanguageRepository
 *
 * @method Language findOneBy(array $criteria, ?array $orderBy = null)
 *
 * @package App\Doctrine\Repository
 */
class LanguageRepository extends EntityRepository
{
    /**
     * @return \App\Doctrine\Entity\Language
     */
    public function findDefault() : Language
    {
        return $this->findOneBy(['isDefault' => true]);
    }

    /**
     * @param string $code
     *
     * @return \App\Doctrine\Entity\Language|null
     */
    public function findByCode(string $code) : ?Language
    {
        return $this->findOneBy(['code' => $code]);
    }
}
