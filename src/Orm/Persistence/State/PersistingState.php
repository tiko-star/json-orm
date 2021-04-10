<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Widget;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;
use Symfony\Component\Uid\Uuid;

class PersistingState implements SerializationStateInterface
{
    public function serialize(AbstractEntity $entity) : array
    {
        if (null === $entity->getHash()) {
            $entity->setHash((string) Uuid::v1());
        }

        $data = [
            'type' => $entity->getType(),
            'hash' => $entity->getHash(),
        ];

        if ($entity instanceof ContainsChildrenInterface) {
            $data['children'] = $entity->getChildren();
        }

        if ($entity instanceof Widget) {
            $data['widgetType'] = $entity->getWidgetType();
        }

        return $data;
    }
}
