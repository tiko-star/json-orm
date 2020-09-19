<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Orm\Entity\AbstractEntity;

interface SerializationStateInterface
{
    /**
     * Serialize LayoutObject instance.
     *
     * @param \App\Orm\Entity\AbstractEntity $entity
     *
     * @return array
     */
    public function serialize(AbstractEntity $entity) : array;
}