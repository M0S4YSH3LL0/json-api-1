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

use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;

/**
 * Class TestHydrator
 * @package CloudCreativity\JsonApi
 */
class TestHydrator extends AbstractHydrator
{

    use HydratesAttributesTrait;

    /**
     * The attributes that can be hydrated
     *
     * @var array|null
     */
    public $attributes;

    /**
     * Attributes to cast as dates
     *
     * @var array|null
     */
    public $dates;

    /**
     * @inheritDoc
     */
    protected function hydrateAttribute($record, $attrKey, $value)
    {
        $record->{$attrKey} = $value;
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function hydrateUserRelationship(RelationshipInterface $relationship, $record)
    {
        $record->user_id = $relationship->getIdentifier()->getId();
    }

    /**
     * @param RelationshipInterface $relationship
     * @param $record
     */
    protected function hydrateLatestTagsRelationship(RelationshipInterface $relationship, $record)
    {
        $record->tag_ids = $relationship->getIdentifiers()->getIds();
    }

}
