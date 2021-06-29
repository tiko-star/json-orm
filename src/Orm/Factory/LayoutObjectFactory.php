<?php

declare(strict_types = 1);

namespace App\Orm\Factory;

use App\Orm\Definition\EntityDefinition;
use App\Orm\Definition\EntityDefinitionProvider;
use App\Orm\Definition\Exception\DefinitionException;
use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Decorators\ValidationAwareEntityDecorator;
use App\Orm\Entity\Hash;
use App\Orm\Entity\Widget;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use App\Orm\Entity\Decorators\ContainerEntityDecorator;
use App\Orm\Exception\InvalidEntityHashException;
use App\Orm\Exception\InvalidEntityTypeException;
use App\Orm\Exception\MissingEntityTypeIdentifierException;
use App\Orm\Persistence\LayoutObject;
use App\Orm\Persistence\ReferenceAwareEntityCollection;
use App\Orm\Persistence\State\SerializationStateInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function sprintf;

/**
 * Complex factory for creating instances of LayoutObject.
 *
 * @see     \App\Orm\Persistence\LayoutObject
 *
 * @package App\Orm\Factory
 */
class LayoutObjectFactory
{
    /**
     * @var \App\Orm\Definition\EntityDefinitionProvider Reference on EntityDefinitionProvider instance.
     */
    protected EntityDefinitionProvider $definitionProvider;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface Reference on PropertyAccessorInterface instance.
     */
    protected PropertyAccessorInterface $propertyAccessor;

    /**
     * LayoutObjectFactory constructor.
     *
     * Initialize PropertyAccessor without exception support.
     *
     * @param \App\Orm\Definition\EntityDefinitionProvider $definitionProvider
     */
    public function __construct(EntityDefinitionProvider $definitionProvider)
    {
        $this->definitionProvider = $definitionProvider;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidIndex()
            ->disableExceptionOnInvalidPropertyPath()
            ->enableMagicCall()
            ->getPropertyAccessor();
    }

    /**
     * Create instance of LayoutObject with given content inside.
     *
     * @param array                                                       $content
     * @param string|null                                                 $hash
     * @param \App\Orm\Persistence\State\SerializationStateInterface|null $state
     *
     * @return \App\Orm\Persistence\LayoutObject
     * @throws \App\Orm\Exception\InvalidEntityHashException
     * @throws \App\Orm\Exception\InvalidEntityTypeException
     * @throws \App\Orm\Exception\MissingEntityTypeIdentifierException
     */
    public function createLayoutObject(array $content, string $hash = null, SerializationStateInterface $state = null) : LayoutObject
    {
        $layoutObject = new LayoutObject($hash, $state);
        $tree = $this->hydrate($content, $layoutObject);

        $layoutObject->setTree($tree);

        return $layoutObject;
    }

    /**
     * Recursively iterate over the given content and initialize appropriate AbstractEntity instances.
     *
     * @param array                             $content
     * @param \App\Orm\Persistence\LayoutObject $layoutObject
     *
     * @return \App\Orm\Persistence\ReferenceAwareEntityCollection
     * @throws \App\Orm\Exception\InvalidEntityHashException
     * @throws \App\Orm\Exception\InvalidEntityTypeException
     * @throws \App\Orm\Exception\MissingEntityTypeIdentifierException
     */
    protected function hydrate(array $content, LayoutObject $layoutObject) : ReferenceAwareEntityCollection
    {
        $collection = new ReferenceAwareEntityCollection();
        $collection->setReference($layoutObject);

        foreach ($content as $item) {
            $entity = $this->createEntityInstance($item);
            $collection[] = $entity;
            $layoutObject->getHashMap()->set((string) $entity->getHash(), $entity);

            if (($children = $this->propertyAccessor->getValue($item, '[children]'))
                && $entity->getDefinition()->containsChildren()) {
                /** @var ContainsChildrenInterface $entity */
                $children = $this->hydrate($children, $layoutObject);

                $entity->setChildren($children);
            }
        }

        return $collection;
    }

    /**
     * Create instance of particular AbstractEntity based on given data.
     *
     * @param array $data
     *
     * @return \App\Orm\Entity\AbstractEntity
     * @throws \App\Orm\Exception\InvalidEntityHashException
     * @throws \App\Orm\Exception\InvalidEntityTypeException
     * @throws \App\Orm\Exception\MissingEntityTypeIdentifierException
     */
    protected function createEntityInstance(array $data) : AbstractEntity
    {
        $type = $this->guessEntityType($data);

        // Look for the entity definition.
        $definition = $this->createEntityDefinition($type);
        $entity = $this->createEntityInstanceFromDefinition($definition);
        $hash = $this->propertyAccessor->getValue($data, '[hash]');

        if (empty($hash)) {
            throw new InvalidEntityHashException('An entity hash can not be empty.');
        }

        $entity->setHash(new Hash($hash));
        // The hash has already been set.
        // We will set children during recursive iterations.
        unset($data['hash'], $data['children']);

        foreach ($data as $property => $value) {
            $this->propertyAccessor->setValue($entity, $property, $value);
        }

        return $entity;
    }

    /**
     * Try to guess a type of the AbstractEntity.
     *
     * @param array $item
     *
     * @return string
     *
     * @throws \App\Orm\Exception\MissingEntityTypeIdentifierException Throw exception if the type identifier is
     *                                                                 missing.
     * @throws \App\Orm\Exception\InvalidEntityTypeException Throw exception if there is something wrong with the type
     *                                                       identifier.
     */
    protected function guessEntityType(array $item) : string
    {
        $type = $this->propertyAccessor->getValue($item, '[type]');

        if (null === $type) {
            throw new MissingEntityTypeIdentifierException('Type identifier is missing');
        }

        if (!is_string($type)) {
            throw new InvalidEntityTypeException(
                sprintf('Invalid entity type: %s', $type)
            );
        }

        return $type;
    }

    /**
     * Create instance of \App\Orm\Definition\EntityDefinition from the source based on the given type.
     *
     * @param string $type
     *
     * @return \App\Orm\Definition\EntityDefinition
     *
     * @throws \App\Orm\Exception\InvalidEntityTypeException Throw exception if there are any issues
     *                                                       during definition compilation.
     */
    protected function createEntityDefinition(string $type) : EntityDefinition
    {
        try {
            return $this->definitionProvider->fetchEntityDefinition($type);
        } catch (InvalidArgumentException|DefinitionException $ex) {
            throw new InvalidEntityTypeException(
                sprintf('Invalid entity type: [%s]', $type),
                0,
                $ex
            );
        }
    }

    /**
     * Create instance of \App\Orm\Entity\AbstractEntity based on the given definition.
     *
     * @param \App\Orm\Definition\EntityDefinition $definition
     *
     * @return \App\Orm\Entity\AbstractEntity
     */
    protected function createEntityInstanceFromDefinition(EntityDefinition $definition) : AbstractEntity
    {
        $entity = new Widget();

        // Once we have instantiated the entity we immediately set the appropriate definition for later usages.
        $entity->setDefinition($definition);

        // Decorate Entity with additional functionalities.
        if ($definition->containsChildren()) {
            $entity = new ContainerEntityDecorator($entity);
        }

        if ($definition->containsValidation()) {
            $entity = new ValidationAwareEntityDecorator($entity);
        }

        return $entity;
    }
}
