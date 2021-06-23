<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

/**
 * Base class for all kind of Grid Entities.
 *
 * @package App\Orm\Entity
 */
class Grid extends AbstractEntity
{
    /**
     * Create array representation of the current entity.
     *
     * @return array
     */
    public function convertToArray() : array
    {
        return [
            'type'   => $this->getType(),
            'hash'   => (string) $this->getHash(),
            'params' => $this->getParams(),
        ];
    }
}
