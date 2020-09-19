<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\AbstractWidget;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;

class DefaultState implements SerializationStateInterface
{
    public function serialize(AbstractEntity $entity) : array
    {
        $data = [
            'type' => $entity->getType(),
            'hash' => $entity->getHash(),
        ];

        if ($entity instanceof ContainsChildrenInterface) {
            $data['children'] = $entity->getChildren();
        }

        if ($entity instanceof AbstractWidget) {
            $data['widgetType'] = $entity->getWidgetType();
        }

        return $data;
    }
}