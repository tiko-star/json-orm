<?php

declare(strict_types = 1);

namespace App\Orm\Persistence\State;

use App\Orm\Definition\PropertyDefinition;
use App\Orm\Persistence\State\Exception\StateException;
use Doctrine\Common\Collections\ArrayCollection;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Rules;
use Respect\Validation\Validatable;
use Respect\Validation\Validator;

/**
 * Base state class for handling entity validation.
 *
 * @package App\Orm\Persistence\State
 */
abstract class AbstractPropsValidationAwareState implements PropsValidationAwareStateInterface
{
    /**
     * @var array Associative array to store validation rules per property type.
     */
    private array $ruleMap;

    /**
     * Create appropriate exception instance which should be thrown in case of validation failure.
     * Class names should be instances of \App\Orm\Persistence\State\Exception\StateException.
     *
     * @param \Respect\Validation\Exceptions\ValidationException $exception
     *
     * @return \App\Orm\Persistence\State\Exception\StateException
     */
    protected abstract function createException(ValidationException $exception) : StateException;

    public function __construct()
    {
        $this->ruleMap = [
            PropertyDefinition::PROPERTY_TYPE_INT    => new Rules\Type('int'),
            PropertyDefinition::PROPERTY_TYPE_STRING => new Rules\Type('string'),
            PropertyDefinition::PROPERTY_TYPE_ARRAY  => new Rules\Type('array'),
            PropertyDefinition::PROPERTY_TYPE_BOOL   => new Rules\Type('bool'),
            PropertyDefinition::PROPERTY_TYPE_DOUBLE => new Rules\Type('double'),
            PropertyDefinition::PROPERTY_TYPE_ANY    => new Rules\AnyOf(
                new Rules\Type('int'),
                new Rules\Type('string'),
                new Rules\Type('array'),
                new Rules\Type('bool'),
                new Rules\Type('double'),
            ),
        ];
    }

    /**
     * Validate props based on their definitions.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $propertyDefinitionList
     * @param array                                        $props
     *
     * @return void
     * @throws \App\Orm\Persistence\State\Exception\StateException
     */
    public function validate(ArrayCollection $propertyDefinitionList, array $props) : void
    {
        foreach ($props as $prop => $value) {
            /** @var \App\Orm\Definition\PropertyDefinition $definition */
            $definition = $propertyDefinitionList->get($prop);

            if (null !== $definition) {
                try {
                    $validatable = $this->createValidatable($definition);
                    $validatable->check($value);
                } catch (ValidationException $e) {
                    throw $this->createException($e);
                }
            }
        }
    }

    /**
     * Create validatable instance based on given definition.
     *
     * @param \App\Orm\Definition\PropertyDefinition $definition
     *
     * @return \Respect\Validation\Validatable
     */
    private function createValidatable(PropertyDefinition $definition) : Validatable
    {
        return new Validator(
            $this->ruleMap[$definition->getPropertyType()]
        );
    }
}
