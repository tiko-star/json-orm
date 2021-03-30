<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use App\Orm\Entity\Utils\HandleChildrenTrait;

class BlockGroup extends AbstractEntity implements ContainsChildrenInterface
{
    use HandleChildrenTrait;
}
