<?php

declare(strict_types = 1);

namespace App\Orm\Entity\Decorators;

use App\Doctrine\Entity\Content;
use App\Orm\Definition\EntityDefinition;
use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Hash;
use App\Orm\Persistence\ReferenceAwareEntityCollection;

use function call_user_func;

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
     * Initialize content data for current entity.
     * This method will only be invoked during JSON serialization process.
     *
     * @param \App\Doctrine\Entity\Content $content
     *
     * @return array
     */
    public function initializeContent(Content $content) : array
    {
        return $this->entity->initializeContent($content);
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->entity->getType();
    }

    /**
     * @param string $type
     */
    public function setType(string $type) : void
    {
        $this->entity->setType($type);
    }

    /**
     * @return \App\Orm\Entity\Hash|null
     */
    public function getHash() : ?Hash
    {
        return $this->entity->getHash();
    }

    /**
     * @param \App\Orm\Entity\Hash $hash
     */
    public function setHash(Hash $hash) : void
    {
        $this->entity->setHash($hash);
    }

    /**
     * @return array
     */
    public function getProperties() : array
    {
        return $this->entity->getProperties();
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties) : void
    {
        $this->entity->setProperties($properties);
    }

    /**
     * @return \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    public function getRoot() : ReferenceAwareEntityCollection
    {
        return $this->entity->getRoot();
    }

    /**
     * @param \App\Orm\Persistence\ReferenceAwareEntityCollection $root
     */
    public function setRoot(ReferenceAwareEntityCollection $root) : void
    {
        $this->entity->setRoot($root);
    }

    /**
     * @return \App\Orm\Definition\EntityDefinition
     */
    public function getDefinition() : EntityDefinition
    {
        return $this->entity->getDefinition();
    }

    /**
     * @param \App\Orm\Definition\EntityDefinition $definition
     */
    public function setDefinition(EntityDefinition $definition) : void
    {
        $this->entity->setDefinition($definition);
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
        return call_user_func([$this->entity, $name], $arguments);
    }
}
