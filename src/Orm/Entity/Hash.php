<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

use function substr;

final class Hash
{
    private const DRAFT_HASH_PREFIX = '__';

    private string $hash;

    public function __construct(string $hash)
    {
        $this->hash = $hash;
    }

    public function isDraft() : bool
    {
        return empty($this->getHash()) || substr($this->getHash(), 0, 2) === self::DRAFT_HASH_PREFIX;
    }

    public function getHash() : string
    {
        return $this->hash;
    }

    public function __toString()
    {
        return $this->getHash();
    }
}
