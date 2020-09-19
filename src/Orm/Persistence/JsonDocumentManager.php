<?php

declare(strict_types = 1);

namespace App\Orm\Persistence;

use App\Orm\Exception\JsonDocumentNotFoundException;

use JsonException;
use function is_readable;
use function sprintf;
use function file_get_contents;
use function json_decode;

/**
 * Utility for working with JSON documents.
 *
 * @package App\Orm\Persistence
 */
class JsonDocumentManager
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
     * Fetch content of the given document.
     *
     * @param string $filename
     *
     * @return array
     *
     * @throws \App\Orm\Exception\JsonDocumentNotFoundException
     * @throws \App\Orm\Persistence\JsonToArrayDecodingErrorException
     */
    public function fetchDocumentContent(string $filename) : array
    {
        $path = $this->root.'/documents/'.$filename.'.json';

        if (!is_readable($path)) {
            throw new JsonDocumentNotFoundException(
                sprintf('Document file not found at path: %s', $path)
            );
        }

        $contents = file_get_contents($path);

        try {
            return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new JsonToArrayDecodingErrorException(
                'Failed to encode the document.',
                0,
                $exception
            );
        }
    }

    public function save(string $filename, string $contents) : void
    {
        $path = $this->root.'/documents/'.$filename.'.json';

        file_put_contents($path, $contents);
    }
}