<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use Doctrine\Common\Collections\ArrayCollection;

interface PropsValidationAwareStateInterface
{
    /**
     * Validate props based on their definitions.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $propertyDefinitionList
     * @param array                                        $props
     *
     * @return void
     */
    public function validate(ArrayCollection $propertyDefinitionList, array $props) : void;
}
