<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Hash;
use App\Orm\Persistence\State\Exception\FailedToPersistException;
use App\Orm\Persistence\State\Exception\StateException;
use Respect\Validation\Exceptions\ValidationException;
use Symfony\Component\Uid\Uuid;

class PersistingState extends AbstractPropsValidationAwareState implements SerializationStateInterface
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
            // Validate props before persistence.
            $this->validate($definition->getPropertyDefinitionList(), $params['props']);

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

        return $data;
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
        return new FailedToPersistException($exception->getMessage(), $exception->getCode(), $exception);
    }
}
