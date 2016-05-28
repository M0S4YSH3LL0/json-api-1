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

namespace CloudCreativity\JsonApi\Hydrator;

use CloudCreativity\JsonApi\Contracts\Hydrator\HydratorInterface;
use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Contracts\Object\RelationshipsInterface;
use CloudCreativity\JsonApi\Contracts\Object\ResourceInterface;
use CloudCreativity\JsonApi\Contracts\Object\StandardObjectInterface;
use CloudCreativity\JsonApi\Exceptions\HydratorException;

abstract class AbstractHydrator implements HydratorInterface
{

    /**
     * @param StandardObjectInterface $attributes
     * @param $record
     * @return void
     */
    abstract protected function hydrateAttributes(StandardObjectInterface $attributes, $record);

    /**
     * Return the method name to call for hydrating the specific relationship.
     *
     * If this method returns an empty value, or a value that is not callable, hydration
     * of the the relationship will be skipped.
     *
     * @param $key
     * @return string|null
     */
    abstract protected function methodForRelationship($key);

    /**
     * Transfer data from a resource to a record.
     *
     * @param ResourceInterface $resource
     * @param object $record
     * @return object
     */
    public function hydrate(ResourceInterface $resource, $record)
    {
        $this->hydrateAttributes($resource->attributes(), $record);
        $this->hydrateRelationships($resource->relationships(), $record);

        return $record;
    }

    /**
     * Transfer data from a resource relationship to a record.
     *
     * @param $relationshipKey
     *      the key of the relationship to hydrate.
     * @param RelationshipInterface $relationship
     *      the relationship object to use for the hydration.
     * @param object $record
     *      the object to hydrate.
     * @return void
     */
    public function hydrateRelationship($relationshipKey, RelationshipInterface $relationship, $record)
    {
        $method = $this->methodForRelationship($relationshipKey);

        if (!$method || !method_exists($this, $method)) {
            throw new HydratorException("Cannot hydrate relationship: $relationshipKey");
        }

        call_user_func([$this, $method], $relationship, $record);
    }

    /**
     * @param RelationshipsInterface $relationships
     * @param $record
     */
    protected function hydrateRelationships(RelationshipsInterface $relationships, $record)
    {
        /** @var RelationshipInterface $relationship */
        foreach ($relationships->all() as $key => $relationship) {
            $method = $this->methodForRelationship($key);

            if (empty($method) || !method_exists($this, $method)) {
                continue;
            }

            call_user_func([$this, $method], $relationship, $record);
        }
    }
}
