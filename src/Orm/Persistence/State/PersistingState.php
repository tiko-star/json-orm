<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Hash;
use Symfony\Component\Uid\Uuid;

class PersistingState implements SerializationStateInterface
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
        $hash = $entity->getHash();

        if ($hash->isDraft()) {
            $entity->setHash(new Hash((string) Uuid::v1()));
        }

        $data = [
            'type' => $entity->getType(),
            'hash' => (string) $entity->getHash(),
        ];

        $params = $entity->getParams();

        if (isset($params['props'])) {
            $content = new Content();
            $content->setHash((string) $entity->getHash());
            $content->setContent($params['props']);
            $entity
                ->getRoot()
                ->getReference()
                ->getContents()
                ->attach($entity->getHash(), $content);
        }

        // Remove content related data.
        unset($params['props']);

        if (!empty($params)) {
            $data['params'] = $params;
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
