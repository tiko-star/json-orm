<?php

declare(strict_types = 1);

namespace App\Orm\Persistence;

use SplObjectStorage;

/**
 * Custom extension of SplObjectStorage.
 * This data structure is only intended for storing Content instances.
 * The hash calculation part is overridden to be applicable for hash comparision.
 *
 * @see     \App\Doctrine\Entity\Content
 * @see     \App\Orm\Entity\Hash
 *
 * @package App\Orm\Persistence
 */
class ContentObjectStorage extends SplObjectStorage
{
    /**
     * Calculate a unique identifier for the contained objects.
     *
     * @param \App\Orm\Entity\Hash $object object whose identifier is to be calculated.
     *
     * @return string A string with the calculated identifier.
     */
    public function getHash($object) : string
    {
        return (string) $object->getHash();
    }
}
