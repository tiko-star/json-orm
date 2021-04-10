<?php

declare(strict_types = 1);

namespace App\Orm\Entity\Decorators;

use App\Orm\Entity\AbstractEntity;

/**
 * Class AbstractDecorator is the base class for all kind of decorators.
 * Defines common methods for working with decorated instances.
 *
 * @package App\Orm\Entity\Decorators
 */
abstract class AbstractDecorator extends AbstractEntity
{
    /**
     * @var \App\Orm\Entity\AbstractEntity Reference on the decorated AbstractEntity instance.
     */
    protected AbstractEntity $entity;

    public function __construct(AbstractEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Forward calls to the decorated instance.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        dd($name);
        return call_user_func([$this->entity, $name], $arguments);
    }
}
