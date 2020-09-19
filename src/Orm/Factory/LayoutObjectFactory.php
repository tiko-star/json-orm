<?php

declare(strict_types = 1);

namespace App\Orm\Factory;

use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Block;
use App\Orm\Entity\BlockGroup;
use App\Orm\Entity\Button;
use App\Orm\Entity\Column;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use App\Orm\Exception\InvalidEntityTypeException;
use App\Orm\Exception\MissingEntityTypeIdentifierException;
use App\Orm\Persistence\LayoutObject;
use App\Orm\Persistence\ReferenceAwareEntityCollection;
use App\Orm\Persistence\State\SerializationStateInterface;
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
        'button'     => Button::class,
    ];

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface Reference on PropertyAccessorInterface instance.
     */
    protected PropertyAccessorInterface $propertyAccessor;

    /**
     * LayoutObjectFactory constructor.
     *
     * Initialize PropertyAccessor without exception support.
     */
    public function __construct()
    {
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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
     */
    protected function createEntityInstance(array $data) : AbstractEntity
    {
        $type = $this->guessEntityType($data);
        $entityClass = $this->propertyAccessor->getValue($this->entityToTypeMapping, "[$type]");

        if (null === $entityClass) {
            throw new InvalidEntityTypeException(
                sprintf('Invalid entity type: %s', $type)
            );
        }

        unset($data['children']);

        $entity = new $entityClass();

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

        if (!is_string($type)) {
            throw new InvalidEntityTypeException(
                sprintf('Invalid entity type: %s', $type)
            );
        }

        return $type;
    }
}