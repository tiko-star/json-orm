<?php

declare(strict_types = 1);

namespace App\Orm\Entity\Contracts;

use App\Orm\Definition\EntityDefinition;

interface ContainsDefinitionInterface
{
    public function getDefinition() : EntityDefinition;

    public function setDefinition(EntityDefinition $definition) : void;
}
