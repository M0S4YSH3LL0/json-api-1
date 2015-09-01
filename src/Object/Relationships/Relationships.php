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

namespace CloudCreativity\JsonApi\Object\Relationships;

use CloudCreativity\JsonApi\Object\StandardObject;

/**
 * Class Relationships
 * @package CloudCreativity\JsonApi
 */
class Relationships extends StandardObject
{

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return parent::get($key);
    }

    /**
     * @param $method
     * @param array $args
     * @return Relationship
     */
    public function __call($method, array $args)
    {
        $matches = [];

        if (!preg_match("/^get{1}(?<relationship>[A-Z]{1}[a-zA-Z]+)$/", $method, $matches)) {
            throw new \BadMethodCallException(sprintf('Cannot call %s::%s()', static::class, $method));
        }

        array_unshift($args, lcfirst($matches['relationship']));

        return call_user_func_array([$this, 'get'], $args);
    }

    /**
     * @param $key
     * @param $default
     * @return Relationship
     */
    public function get($key, $default = null)
    {
        return new Relationship(parent::get($key, $default));
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        foreach ($this->keys() as $key) {
            yield $key => $this->get($key);
        }
    }
}
