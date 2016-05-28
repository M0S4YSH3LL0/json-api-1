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

namespace CloudCreativity\JsonApi\Contracts\Validators;

use CloudCreativity\JsonApi\Contracts\Object\ResourceIdentifierInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;

interface AcceptRelatedResourceInterface
{

    /**
     * Is the specified resource identifier acceptable?
     *
     * @param ResourceIdentifierInterface $identifier
     *      the identifier being validated.
     * @param string|null $key
     *      if validating a resource's relationships, the key that is being validated.
     * @param ResourceInterface|null $resource
     *      if validating a resource's relationships, the resource for context.
     * @return bool
     */
    public function accept(
        ResourceIdentifierInterface $identifier,
        $key = null,
        ResourceInterface $resource = null
    );
}
