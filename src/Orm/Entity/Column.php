<?php

namespace App\Orm\Entity;

use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use App\Orm\Entity\Utils\HandleChildrenTrait;

class Column extends AbstractEntity implements ContainsChildrenInterface
{
    use HandleChildrenTrait;

    public function jsonSerialize()
    {
        return parent::jsonSerialize() + ['children' => $this->getChildren()];
    }
}