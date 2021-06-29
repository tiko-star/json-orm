<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Orm\Entity\AbstractEntity;

class DefaultState implements SerializationStateInterface
{
    public function serialize(AbstractEntity $entity) : array
    {
        $definition = $entity->getDefinition();

        $data = [
            'type' => $entity->getType(),
            'hash' => $entity->getHash(),
        ];

        /** @var \App\Orm\Entity\Contracts\ContainsChildrenInterface $entity */
        if ($definition->containsChildren()) {
            $data['children'] = $entity->getChildren();
        }

        return $data;
    }
}
