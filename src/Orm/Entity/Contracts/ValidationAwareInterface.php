<?php

declare(strict_types = 1);

namespace App\Orm\Entity\Contracts;

interface ValidationAwareInterface
{
    /**
     * Get validation rules for a particular Entity.
     *
     * @return array
     */
    public function getValidationRules() : array;
}
