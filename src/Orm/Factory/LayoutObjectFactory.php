<?php

declare(strict_types = 1);

namespace App\Orm\Factory;

use App\Orm\Definition\EntityDefinition;
use App\Orm\Definition\EntityDefinitionProvider;
use App\Orm\Definition\Exception\DefinitionException;
use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\AbstractWidget;
use App\Orm\Entity\AbstractWidgetItem;
use App\Orm\Entity\Block;
use App\Orm\Entity\BlockGroup;
use App\Orm\Entity\Column;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use App\Orm\Entity\Contracts\ContainsDefinitionInterface;
use App\Orm\Entity\Utils\HandleChildrenTrait;
use App\Orm\Entity\Utils\HandleDefinitionTrait;
use App\Orm\Exception\InvalidEntityTypeException;
use App\Orm\Exception\MissingEntityTypeIdentifierException;
use App\Orm\Persistence\LayoutObject;
use App\Orm\Persistence\ReferenceAwareEntityCollection;
use App\Orm\Persistence\State\SerializationStateInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function array_push;
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
     * @var array<string, string> Mapping of the entity types and appropriate fully qualified class names.
     */
    protected array $entityToTypeMapping = [
        'blockGroup' => BlockGroup::class,
        'block'      => Block::class,
        'column'     => Column::class,
    ];

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
     */
    public function createLayoutObject(array $content, string $hash = null, SerializationStateInterface $state = null) : LayoutObject
    {
        $layoutObject = new LayoutObject($hash, $state);
        $hashes = [];
        $tree = $this->hydrate($content, $hashes, $layoutObject);

        $layoutObject->setHashes($hashes);
        $layoutObject->setTree($tree);

        return $layoutObject;
    }

    /**
     * Recursively iterate over the given content and initialize appropriate AbstractEntity instances.
     *
     * @param array                             $content
     * @param array                             $hashes
     * @param \App\Orm\Persistence\LayoutObject $layoutObject
     *
     * @return \App\Orm\Persistence\ReferenceAwareEntityCollection
     */
    protected function hydrate(array $content, array &$hashes, LayoutObject $layoutObject) : ReferenceAwareEntityCollection
    {
        $collection = new ReferenceAwareEntityCollection();
        $collection->setReference($layoutObject);

        foreach ($content as $item) {
            $entity = $this->createEntityInstance($item);
            $collection[] = $entity;

            if ($hash = $entity->getHash()) {
                array_push($hashes, $hash);
            }

            if (($children = $this->propertyAccessor->getValue($item, '[children]'))
                && $entity instanceof ContainsChildrenInterface) {
                $children = $this->hydrate($children, $hashes, $layoutObject);

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
     * @throws \App\Orm\Exception\InvalidEntityTypeException
     */
    protected function createEntityInstance(array $data) : AbstractEntity
    {
        $type = $this->guessEntityType($data);

        // First try to load an entity instance from mappings.
        $entityClass = $this->propertyAccessor->getValue($this->entityToTypeMapping, "[$type]");

        // If there is no appropriate entity class then we should look for definition.
        if (null === $entityClass) {
            $definition = $this->createEntityDefinition($type);
            $entity = $this->createEntityInstanceFromDefinition($definition);
        } else {
            $entity = new $entityClass();
        }

        unset($data['children']);

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

        if ($widgetType = $this->propertyAccessor->getValue($item, '[widgetType]')) {
            $type = $widgetType;
        }

        if ($widgetItemType = $this->propertyAccessor->getValue($item, '[widgetItemType]')) {
            $type = $widgetItemType;
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
     *
     * @throws \App\Orm\Exception\InvalidEntityTypeException Throw exception if there are any issues
     *                                                       during object creation.
     */
    protected function createEntityInstanceFromDefinition(EntityDefinition $definition) : AbstractEntity
    {
        $entity = null;

        if ($definition->isWidget()) {
            if ($definition->containsChildren()) {
                $entity = new class extends AbstractWidget implements ContainsChildrenInterface, ContainsDefinitionInterface {
                    use HandleChildrenTrait,
                        HandleDefinitionTrait;
                };
            } else {
                $entity = new class extends AbstractWidget implements ContainsDefinitionInterface {
                    use HandleDefinitionTrait;
                };
            }
        }

        if ($definition->isWidgetItem()) {
            if ($definition->containsChildren()) {
                $entity = new class extends AbstractWidgetItem implements ContainsChildrenInterface, ContainsDefinitionInterface {
                    use HandleChildrenTrait,
                        HandleDefinitionTrait;
                };
            } else {
                $entity = new class extends AbstractWidgetItem implements ContainsDefinitionInterface {
                    use HandleDefinitionTrait;
                };
            }
        }

        if (null === $entity) {
            throw new InvalidEntityTypeException(
                sprintf('Invalid entity type: %s', $definition->getName())
            );
        }

        $entity->setDefinition($definition);

        return $entity;
    }
}
