<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

use function substr;

/**
 * Object-oriented representation of the unique hashes of every AbstractEntity.
 *
 * @package App\Orm\Entity
 */
final class Hash
{
    private const DRAFT_HASH_PREFIX = '__';

    /**
     * @var string Internal value of the hash.
     */
    private string $hash;

    public function __construct(string $hash)
    {
        $this->hash = $hash;
    }

    /**
     * Determine whether the hash is draft or not.
     * Draft hashes are those hashes that start with the special prefix.
     * Draft hashes basically are generated by the client.
     *
     * @return bool
     */
    public function isDraft() : bool
    {
        return empty($this->getHash()) || substr($this->getHash(), 0, 2) === self::DRAFT_HASH_PREFIX;
    }

    /**
     * Get internal hash value for current instance.
     *
     * @return string
     */
    public function getHash() : string
    {
        return $this->hash;
    }

    /**
     * Convert instance to string value.
     * Conversion returns internal hash value.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getHash();
    }
}
