<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\AbstractWidget;
use App\Orm\Entity\Contracts\ContainsChildrenInterface;

class FetchedState implements SerializationStateInterface
{
    public function serialize(AbstractEntity $entity) : array
    {
        $contents = $entity->getRoot()->getReference()->getContents();

        /** @var Content|false $content */
        $content = $contents
            ->filter(fn(Content $content) => $content->getHash() === $entity->getHash())
            ->first();

        if ($content) {
            $entity->initializeContent($content);
        }

        $data = [
            'type'  => $entity->getType(),
            'hash'  => $entity->getHash(),
            'props' => $entity->getProperties(),
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