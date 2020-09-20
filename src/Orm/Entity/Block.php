<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use App\Orm\Entity\Utils\HandleChildrenTrait;

class Block extends AbstractEntity implements ContainsChildrenInterface
{
    use HandleChildrenTrait;

    public function jsonSerialize() : array
    {
        return parent::jsonSerialize() + ['children' => $this->getChildren()];
    }

    public function initializeContent(Content $content) : array
    {
        return [];
    }
}