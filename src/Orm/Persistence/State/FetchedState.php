<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\AbstractWidget;
use App\Orm\Entity\AbstractWidgetItem;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;

class FetchedState implements SerializationStateInterface
{
    public function serialize(AbstractEntity $entity) : array
    {
        $data = [
            'type' => $entity->getType(),
            'hash' => $entity->getHash(),
        ];

        $contents = $entity->getRoot()->getReference()->getContents();

        /** @var Content|false $content */
        $content = $contents
            ->filter(fn(Content $content) => $content->getHash() === $entity->getHash())
            ->first();

        if ($content) {
            $data['props'] = $entity->initializeContent($content);
        }

        if ($entity instanceof ContainsChildrenInterface) {
            $data['children'] = $entity->getChildren();
        }

        if ($entity instanceof AbstractWidget) {
            $data['widgetType'] = $entity->getWidgetType();
        }

        if ($entity instanceof AbstractWidgetItem) {
            $data['widgetItemType'] = $entity->getWidgetItemType();
        }

        return $data;
    }
}
