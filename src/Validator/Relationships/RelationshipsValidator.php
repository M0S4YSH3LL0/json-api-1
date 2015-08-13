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

namespace CloudCreativity\JsonApi\Validator\Relationships;

use CloudCreativity\JsonApi\Validator\AbstractValidator;
use CloudCreativity\JsonApi\Contracts\Validator\ValidatorInterface;
use CloudCreativity\JsonApi\Error\ErrorObject;
use CloudCreativity\JsonApi\Validator\Helper\RequiredKeysTrait;

/**
 * Class RelationshipsValidator
 * @package CloudCreativity\JsonApi
 */
class RelationshipsValidator extends AbstractValidator
{

    use RequiredKeysTrait;

    const ERROR_INVALID_VALUE = 'invalid-value';
    const ERROR_UNRECOGNISED_RELATIONSHIP = 'not-recognised';
    const ERROR_REQUIRED_RELATIONSHIP = 'required';

    /**
     * @var array
     */
    protected $templates = [
        self::ERROR_INVALID_VALUE => [
            ErrorObject::CODE => self::ERROR_INVALID_VALUE,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Invalid Value',
            ErrorObject::DETAIL => 'Invalid relationships object value.',
        ],
        self::ERROR_REQUIRED_RELATIONSHIP => [
            ErrorObject::CODE => self::ERROR_REQUIRED_RELATIONSHIP,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Required Relationship',
            ErrorObject::DETAIL => 'Missing required relationship "%s".',
        ],
        self::ERROR_UNRECOGNISED_RELATIONSHIP => [
            ErrorObject::CODE => self::ERROR_UNRECOGNISED_RELATIONSHIP,
            ErrorObject::STATUS => 400,
            ErrorObject::TITLE => 'Unrecognised Relationship',
            ErrorObject::DETAIL => 'Relationship is not recognised and cannot be accepted.',
        ],
    ];

    /**
     * @var array
     */
    protected $_validators = [];

    /**
     * @param ValidatorInterface[] $validators
     * @return $this
     */
    public function setValidators(array $validators)
    {
        foreach ($validators as $key => $validator) {

            if (!$validator instanceof ValidatorInterface) {
                throw new \InvalidArgumentException('Expecting array to only contain ValidatorInterface objects.');
            }

            $this->setValidator($key, $validator);
        }

        return $this;
    }

    /**
     * @param $key
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setValidator($key, ValidatorInterface $validator)
    {
        $this->_validators[$key] = $validator;

        return $this;
    }

    /**
     * @param $key
     * @return ValidatorInterface
     */
    public function getValidator($key)
    {
        if (!$this->hasValidator($key)) {
            throw new \RuntimeException(sprintf('No validator set for "%s".', $key));
        }

        return $this->_validators[$key];
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasValidator($key)
    {
        return isset($this->_validators[$key]);
    }

    /**
     * Helper method to add a belongs to validator for the specified key.
     *
     * @param $key
     * @param $typeOrTypes
     * @param array $options
     * @return $this
     */
    public function belongsTo($key, $typeOrTypes, array $options = [])
    {
        $validator = new BelongsToValidator($typeOrTypes);
        $validator->configure($options);

        $this->setValidator($key, $validator);

        return $this;
    }

    /**
     * Helper method to add a has-many validator for the specified key.
     *
     * @param $key
     * @param $typeOrTypes
     * @param array $options
     * @return $this
     */
    public function hasMany($key, $typeOrTypes, array $options = [])
    {
        $validator = new HasManyValidator($typeOrTypes);
        $validator->configure($options);

        $this->setValidator($key, $validator);

        return $this;
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

        // Validate each provided relationship
        foreach (get_object_vars($value) as $key => $v) {
            $this->checkKey($key)
                ->checkValue($key, $v);
        }

        // Check required relationships
        foreach ($this->getRequiredKeys() as $key) {

            if (!isset($value->{$key})) {
                $error = $this->error(static::ERROR_REQUIRED_RELATIONSHIP);
                $error->setDetail(sprintf($error->getDetail(), $key));
            }
        }
    }

    /**
     * @param $key
     * @return $this
     */
    protected function checkKey($key)
    {
        if (!$this->hasValidator($key)) {
            $this->error(static::ERROR_UNRECOGNISED_RELATIONSHIP)
                ->source()
                ->setPointer(sprintf('/%s', $key));
        }

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    protected function checkValue($key, $value)
    {
        if (!$this->hasValidator($key)) {
            return $this;
        }

        $validator = $this->getValidator($key);

        if (!$validator->isValid($value)) {
            $this->getErrors()
                ->merge($validator
                    ->getErrors()
                    ->setSourcePointer(function ($current) use ($key) {
                        return '/' . $key . $current;
                    }));
        }

        return $this;
    }
}
