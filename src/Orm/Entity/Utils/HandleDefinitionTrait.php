<?php

declare(strict_types = 1);

namespace App\Orm\Entity\Utils;

use App\Orm\Definition\EntityDefinition;

trait HandleDefinitionTrait
{
    protected EntityDefinition $definition;

    /**
     * @return \App\Orm\Definition\EntityDefinition
     */
    public function getDefinition() : EntityDefinition
    {
        return $this->definition;
    }

    /**
     * @param \App\Orm\Definition\EntityDefinition $definition
     */
    public function setDefinition(EntityDefinition $definition) : void
    {
        $this->definition = $definition;
    }
}
