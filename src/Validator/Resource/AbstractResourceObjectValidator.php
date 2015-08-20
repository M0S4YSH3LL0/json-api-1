<?php

/**
 * Copyright 2015 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Validator\Resource;

use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Object\Resource\ResourceObject;
use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\Validator\AbstractValidator;

/**
 * Class AbstractResourceValidator
 * @package CloudCreativity\JsonApi
 */
abstract class AbstractResourceObjectValidator extends AbstractValidator
{

    const ERROR_INVALID_VALUE = 'invalid-value';
    const ERROR_MISSING_TYPE = 'missing-type';
    const ERROR_MISSING_ID = 'missing-id';
    const ERROR_UNEXPECTED_ID = 'unexpected-id';
    const ERROR_MISSING_ATTRIBUTES = 'missing-attributes';
    const ERROR_MISSING_RELATIONSHIPS = 'missing-relationships';
    const ERROR_UNEXPECTED_RELATIONSHIPS = 'unexpected-relationships';

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
            ErrorObject::DETAIL => 'Resource must be an object.',
        ],
        self::ERROR_MISSING_TYPE => [
            ErrorObject::CODE => self::ERROR_MISSING_TYPE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Missing Resource Type',
            ErrorObject::DETAIL => 'Resource object must have a type member.',
        ],
        self::ERROR_MISSING_ID => [
            ErrorObject::CODE => self::ERROR_MISSING_ID,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Missing Resource ID',
            ErrorObject::DETAIL => 'Resource object must have an id member.',
        ],
        self::ERROR_UNEXPECTED_ID => [
            ErrorObject::CODE => self::ERROR_UNEXPECTED_ID,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Unexpected Resource ID',
            ErrorObject::DETAIL => 'Not expecting resource object to have an id member.',
        ],
        self::ERROR_MISSING_ATTRIBUTES => [
            ErrorObject::CODE => self::ERROR_MISSING_ATTRIBUTES,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Missing Resource Attributes',
            ErrorObject::DETAIL => 'Resource object must have an attributes member.',
        ],
        self::ERROR_MISSING_RELATIONSHIPS => [
            ErrorObject::CODE => self::ERROR_MISSING_RELATIONSHIPS,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Missing Resource Relationships',
            ErrorObject::DETAIL => 'Resource object must have a relationships member.',
        ],
        self::ERROR_UNEXPECTED_RELATIONSHIPS => [
            ErrorObject::CODE => self::ERROR_UNEXPECTED_RELATIONSHIPS,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Unexpected Resource Relationships',
            ErrorObject::DETAIL => 'Not expecting resource object to have a relationships member.',
        ],
    ];

    /**
     * @return ValidatorInterface
     */
    abstract public function getTypeValidator();

    /**
     * Get the id validator or null if no id is expected.
     *
     * @return ValidatorInterface|null
     */
    abstract public function getIdValidator();

    /**
     * Get the attributes validator or null if the resource object must not have attributes.
     *
     * @return ValidatorInterface|null
     */
    abstract public function getAttributesValidator();

    /**
     * Get the relationships validator or null if the resource object must not have relationships.
     *
     * @return ValidatorInterface|null
     */
    abstract public function getRelationshipsValidator();

    /**
     * @return bool
     */
    public function hasIdValidator()
    {
        return $this->getIdValidator() instanceof ValidatorInterface;
    }

    /**
     * Whether the resource object expects to have an id.
     *
     * @return bool
     */
    public function isExpectingId()
    {
        $id = $this->getIdValidator();

        return ($id instanceof ValidatorInterface && $id->isRequired());
    }

    /**
     * Whether the resource object expects to have attributes.
     *
     * @return bool
     */
    public function isExpectingAttributes()
    {
        $attr = $this->getAttributesValidator();

        return ($attr instanceof ValidatorInterface && $attr->isRequired());
    }

    /**
     * Whether the resource object expects to have relationships.
     *
     * @return bool
     */
    public function isExpectingRelationships()
    {
        $rel = $this->getRelationshipsValidator();

        return ($rel instanceof ValidatorInterface && $rel->isRequired());
    }

    /**
     * @param $value
     */
    protected function validate($value)
    {
        if (!is_object($value)) {
            $this->error(static::ERROR_INVALID_VALUE);
            return;
        }

        $object = new StandardObject($value);

        $this->validateType($object)
            ->validateId($object)
            ->validateAttributes($object)
            ->validateRelationships($object);
    }

    /**
     * @param StandardObject $object
     * @return $this
     */
    protected function validateType(StandardObject $object)
    {
        $type = $this->getTypeValidator();

        if ($type->isRequired() && !$object->has(ResourceObject::TYPE)) {
            $this->error(static::ERROR_MISSING_TYPE);
            return $this;
        }

        if (!$type->isValid($object->get(ResourceObject::TYPE))) {
            $this->getErrors()
                ->merge($type
                    ->getErrors()
                    ->setSourcePointer('/' . ResourceObject::TYPE));
        }

        return $this;
    }

    /**
     * @param StandardObject $object
     * @return $this
     */
    protected function validateId(StandardObject $object)
    {
        $expectation = $this->isExpectingId();

        // Is valid if no id and $this does not have an id validator.
        if (!$object->has(ResourceObject::ID) && !$expectation) {
            return $this;
        }

        if (!$object->has(ResourceObject::ID) && $expectation) {
            $this->error(static::ERROR_MISSING_ID, '/');
            return $this;
        } elseif ($object->has(ResourceObject::ID) && !$this->hasIdValidator()) {
            $this->error(static::ERROR_UNEXPECTED_ID, '/' . ResourceObject::ID);
            return $this;
        }

        $validator = $this->getIdValidator();

        if (!$validator->isValid($object->get(ResourceObject::ID))) {
            $this->getErrors()
                ->merge($validator
                    ->getErrors()
                    ->setSourcePointer('/' . ResourceObject::ID));
        }

        return $this;
    }

    /**
     * @param StandardObject $object
     * @return $this
     */
    protected function validateAttributes(StandardObject $object)
    {
        $expectation = $this->isExpectingAttributes();

        // valid if the object does not have attributes, and attributes are not expected.
        if (!$object->has(ResourceObject::ATTRIBUTES) && !$expectation) {
            return $this;
        }

        if (!$object->has(ResourceObject::ATTRIBUTES) && $expectation) {
            $this->error(static::ERROR_MISSING_ATTRIBUTES);
            return $this;
        }

        $validator = $this->getAttributesValidator();

        if (!$validator->isValid($object->get(ResourceObject::ATTRIBUTES))) {
            $this->getErrors()
                ->merge($validator
                    ->getErrors()
                    ->setSourcePointer(function ($current) {
                        return sprintf('/%s%s', ResourceObject::ATTRIBUTES, $current);
                    }));
        }

        return $this;
    }

    /**
     * @param StandardObject $object
     * @return $this
     */
    protected function validateRelationships(StandardObject $object)
    {
        $expectation = $this->isExpectingRelationships();

        // valid if no relationships and not expecting relationships
        if (!$object->has(ResourceObject::RELATIONSHIPS) && !$expectation) {
            return $this;
        }

        if (!$object->has(ResourceObject::RELATIONSHIPS) && $expectation) {
            $this->error(static::ERROR_MISSING_RELATIONSHIPS);
            return $this;
        }

        $validator = $this->getRelationshipsValidator();

        if (!$validator->isValid($object->get(ResourceObject::RELATIONSHIPS))) {
            $this->getErrors()
                ->merge($validator
                    ->getErrors()
                    ->setSourcePointer(function ($current) {
                        return sprintf('/%s%s', ResourceObject::RELATIONSHIPS, $current);
                    }));
        }

        return $this;
    }
}
