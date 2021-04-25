<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\AbstractEntity;

/**
 * Implements serialization for AbstractEntity when the entity is in fetched state.
 *
 * @package App\Orm\Persistence\State
 */
class FetchedState implements SerializationStateInterface
{
    /**
     * Specify data which should be serialized to JSON.
     *
     * @param \App\Orm\Entity\AbstractEntity $entity
     *
     * @return array
     */
    public function serialize(AbstractEntity $entity) : array
    {
        $definition = $entity->getDefinition();

        $data = [
            'type' => $entity->getType(),
            'hash' => (string) $entity->getHash(),
        ];

        $params = $entity->getParams();

        if (isset($params['css'])) {
            $data['params']['css'] = $params['css'];
        }

        $content = $this->findEntityContent($entity);

        if (null !== $content && !empty($content->getContent())) {
            $data['params']['props'] = $entity->initializeContent($content);
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

    protected function findEntityContent(AbstractEntity $entity) : ?Content
    {
        $contents = $entity->getRoot()->getReference()->getContents();
        $hash = $entity->getHash();

        if ($contents->contains($hash)) {
            /** @var Content $content */
            $content = $contents[$hash];

            return $content;
        }

        return null;
    }
}
