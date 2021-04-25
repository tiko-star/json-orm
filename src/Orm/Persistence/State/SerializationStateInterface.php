<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Orm\Entity\AbstractEntity;

/**
 * Interface JsonSerializeStateInterface declares serialization methods for AbstractEntity instances.
 *
 * @see     \App\Orm\Entity\AbstractEntity
 * @package App\Orm\Persistence\State
 */
interface SerializationStateInterface
{
    /**
     * Specify data which should be serialized to JSON.
     *
     * @param \App\Orm\Entity\AbstractEntity $entity
     *
     * @return array
     */
    public function serialize(AbstractEntity $entity) : array;
}
