<?php

/**
 * Copyright 2016 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Validators;

use CloudCreativity\JsonApi\Contracts\Object\DocumentInterface;
use CloudCreativity\JsonApi\Contracts\Validators\DocumentValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\RelationshipValidatorInterface;
use CloudCreativity\JsonApi\Contracts\Validators\ValidationMessageFactoryInterface;

class RelationshipDocumentValidator extends AbstractValidator implements DocumentValidatorInterface
{

    /**
     * @var RelationshipValidatorInterface
     */
    private $relationshipValidator;

    /**
     * RelationshipDocumentValidator constructor.
     * @param ValidationMessageFactoryInterface $messages
     * @param RelationshipValidatorInterface $validator
     */
    public function __construct(
        ValidationMessageFactoryInterface $messages,
        RelationshipValidatorInterface $validator
    ) {
        parent::__construct($messages);
        $this->relationshipValidator = $validator;
    }

    /**
     * @param DocumentInterface $document
     * @return bool
     */
    public function isValid(DocumentInterface $document)
    {
        $this->reset();

        if (!$this->relationshipValidator->isValid($document->relationship())) {
            $this->addErrors($this->relationshipValidator->errors());
            return false;
        }

        return true;
    }

}
