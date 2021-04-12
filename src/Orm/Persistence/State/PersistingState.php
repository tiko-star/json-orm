<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Hash;
use Symfony\Component\Uid\Uuid;

class PersistingState implements SerializationStateInterface
{
    public function serialize(AbstractEntity $entity) : array
    {
        $definition = $entity->getDefinition();

        if ($entity->getHash()->isDraft()) {
            $entity->setHash(new Hash((string) Uuid::v1()));
        }

        $data = [
            'type' => $entity->getType(),
            'hash' => (string) $entity->getHash(),
        ];

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface $entity */
        if ($definition->containsChildren()) {
            $data['children'] = $entity->getChildren();
        }

        /** @var \App\Orm\Entity\Widget $entity */
        if ($definition->isWidget()) {
            $data['widgetType'] = $entity->getWidgetType();
        }

        /** @var \App\Orm\Entity\WidgetItem $entity */
        if ($definition->isWidgetItem()) {
            $data['widgetItemType'] = $entity->getWidgetItemType();
        }

        return $data;
    }
}
