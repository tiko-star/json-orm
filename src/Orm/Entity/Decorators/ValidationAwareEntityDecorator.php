<?php

declare(strict_types = 1);

namespace App\Orm\Entity\Decorators;

use App\Orm\Entity\Contracts\ValidationAwareInterface;

/**
 * Decorator for enabling data validation support for AbstractEntities.
 * Defines methods for validation ruleset retrieval.
 *
 * @package App\Orm\Entity\Decorators
 */
class ValidationAwareEntityDecorator extends AbstractDecorator implements ValidationAwareInterface
{
    /**
     * Get validation ruleset for specific Entity.
     *
     * @return array
     */
    public function getValidationRules() : array
    {
        $params = $this->getParams();

        return $params['validation'] ?? [];
    }
}
