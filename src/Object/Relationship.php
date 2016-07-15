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

namespace CloudCreativity\JsonApi\Object;

use CloudCreativity\JsonApi\Contracts\Object\RelationshipInterface;
use CloudCreativity\JsonApi\Exceptions\DocumentException;
use CloudCreativity\JsonApi\Object\Helpers\MetaMemberTrait;

/**
 * Class Relationship
 * @package CloudCreativity\JsonApi
 */
class Relationship extends StandardObject implements RelationshipInterface
{

    use MetaMemberTrait;

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->isHasOne()) {
            return $this->getIdentifier();
        } elseif ($this->isHasMany()) {
            return $this->getIdentifiers();
        }

        throw new DocumentException('No data member or data member is not a valid relationship.');
    }


    /**
     * @inheritdoc
     */
    public function getIdentifier()
    {
        if (!$this->isHasOne()) {
            throw new DocumentException('No data member or data member is not a valid has-one relationship.');
        }

        $data = $this->get(self::DATA);

        return ($data) ? new ResourceIdentifier($data) : null;
    }

    /**
     * @inheritdoc
     */
    public function isHasOne()
    {
        if (!$this->has(self::DATA)) {
            return false;
        }

        $data = $this->get(self::DATA);

        return is_null($data) || is_object($data);
    }

    /**
     * @inheritdoc
     */
    public function getIdentifiers()
    {
        if (!$this->isHasMany()) {
            throw new DocumentException('No data member of data member is not a valid has-many relationship.');
        }

        return ResourceIdentifierCollection::create($this->get(self::DATA));
    }

    /**
     * @inheritdoc
     */
    public function isHasMany()
    {
        return is_array($this->get(self::DATA));
    }
}
