<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

/**
 * Base class for all kind of Widgets.
 * Contains all general methods.
 *
 * @package App\Orm\Entity
 */
class Widget extends AbstractEntity
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
