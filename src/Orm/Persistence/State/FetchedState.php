<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\AbstractEntity;
use App\Orm\Persistence\State\Exception\FailedToFetchException;
use App\Orm\Persistence\State\Exception\StateException;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Implements serialization for AbstractEntity when the entity is in fetched state.
 *
 * @package App\Orm\Persistence\State
 */
class FetchedState extends AbstractPropsValidationAwareState implements SerializationStateInterface
{
    /**
     * Specify data which should be serialized to JSON.
     *
     * @param \App\Orm\Entity\AbstractEntity $entity
     *
     * @return array
     * @throws \App\Orm\Persistence\State\Exception\StateException
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
            $props = $entity->initializeContent($content);

            // Validate props before assignment.
            $this->validate($definition->getPropertyDefinitionList(), $props);
            $data['params']['props'] = $props;
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

    /**
     * Create appropriate exception instance which should be thrown in case of validation failure.
     * Class names should be instances of \App\Orm\Persistence\State\Exception\StateException.
     *
     * @param \Respect\Validation\Exceptions\ValidationException $exception
     *
     * @return \App\Orm\Persistence\State\Exception\StateException
     */
    protected function createException(ValidationException $exception) : StateException
    {
        return new FailedToFetchException($exception->getMessage(), $exception->getCode(), $exception);
    }
}
