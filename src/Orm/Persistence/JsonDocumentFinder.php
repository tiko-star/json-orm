<?php

namespace App\Orm\Persistence;

use InvalidArgumentException;

class JsonDocumentFinder
{
    /**
     * @param string $filename
     *
     * @return array
     * @throws \JsonException
     */
    public function fetchDocumentContent(string $filename) : array
    {
        $path = ROOT_DIR.'/documents/'.$filename;

        if (!is_readable($path)) {
            throw new InvalidArgumentException('Document file not found!');
        }

        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }
}