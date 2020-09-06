<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use App\Orm\Entity\Utils\HandleChildrenTrait;

class Column extends AbstractEntity implements ContainsChildrenInterface
{
    use HandleChildrenTrait;

    public function jsonSerialize() : array
    {
        return parent::jsonSerialize() + ['children' => $this->getChildren()];
    }

    protected function initializeContent(Content $content) : void
    {
    }
}