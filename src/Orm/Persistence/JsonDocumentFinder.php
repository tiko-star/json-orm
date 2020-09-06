<?php

declare(strict_types = 1);

namespace App\Orm\Persistence;

use InvalidArgumentException;

/**
 * Utility for working with JSON documents.
 *
 * @package App\Orm\Persistence
 */
class JsonDocumentFinder
{
    /**
     * @var string Root directory.
     */
    protected string $root;

    public function __construct(string $root)
    {
        $this->root = $root;
    }

    /**
     * @param string $filename
     *
     * @return array
     * @throws \JsonException
     */
    public function fetchDocumentContent(string $filename) : array
    {
        $path = $this->root.'/documents/'.$filename;

        if (!is_readable($path)) {
            throw new InvalidArgumentException('Document file not found!');
        }

        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }
}