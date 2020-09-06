<?php

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
use Symfony\Component\PropertyAccess\PropertyAccess;

class LayoutObjectFactory
{
    protected $entityToTypeMapping = [
        'blockGroup' => BlockGroup::class,
        'block'      => Block::class,
        'column'     => Column::class,
        'button'     => Button::class,
    ];

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidIndex()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();
    }

    public function createLayoutObject(array $content) : LayoutObject
    {
        $hashes = [];
        $tree = $this->hydrate($content, $hashes);

        return new LayoutObject($tree, $hashes);
    }

    protected function hydrate(array $content, array &$hashes) : ReferenceAwareEntityCollection
    {
        $collection = new ReferenceAwareEntityCollection();

        foreach ($content as $item) {
            $entity = $this->createEntityInstance($item);
            $collection[] = $entity;

            if ($hash = $entity->getHash()) {
                array_push($hashes, $hash);
            }

            if (($children = $this->propertyAccessor->getValue($item, '[children]'))
                && $entity instanceof ContainsChildrenInterface) {
                $children = $this->hydrate($children, $hashes);

                $entity->setChildren($children);
            }
        }

        return $collection;
    }

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