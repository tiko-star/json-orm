<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\AbstractEntity;

class FetchedState implements SerializationStateInterface
{
    public function serialize(AbstractEntity $entity) : array
    {
        $definition = $entity->getDefinition();

        $data = [
            'type' => $entity->getType(),
            'hash' => (string) $entity->getHash(),
        ];

        $contents = $entity->getRoot()->getReference()->getContents();

        /** @var Content|false $content */
        $content = $contents
            ->filter(fn(Content $content) => $content->getHash() === (string) $entity->getHash())
            ->first();

        if ($content) {
            $data['props'] = $entity->initializeContent($content);
        }

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
