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

namespace CloudCreativity\JsonApi\Error;

use CloudCreativity\JsonApi\Contracts\Error\ErrorObjectInterface;
use CloudCreativity\JsonApi\Contracts\Error\SourceObjectInterface;
use Neomerx\JsonApi\Contracts\Schema\LinkInterface;

/**
 * Class ErrorObject
 * @package CloudCreativity\JsonApi
 */
class ErrorObject implements ErrorObjectInterface
{

    protected $_id;
    protected $_links;
    protected $_status;
    protected $_code;
    protected $_title;
    protected $_detail;
    protected $_source;
    protected $_meta;

    /**
     * @param array $input
     */
    public function __construct(array $input = [])
    {
        $this->exchangeArray($input);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        if (is_object($this->_source)) {
            $this->_source = clone $this->_source;
        }
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * Get a unique identifier for this particular occurrence of the problem.
     *
     * @return int|string|null
     */
    public function getId()
    {
        if (is_int($this->_id)) {
            return $this->_id;
        }

        return !is_null($this->_id) ? (string) $this->_id : null;
    }

    /**
     * @param $links
     * @return $this
     */
    public function setLinks($links)
    {
        $this->_links = $links;

        return $this;
    }

    /**
     * Get links that may lead to further details about this particular occurrence of the problem.
     *
     * @return null|array<string,\Neomerx\JsonApi\Contracts\Schema\LinkInterface>
     */
    public function getLinks()
    {
        if (is_string($this->_links) || is_array($this->_links) || $this->_links instanceof LinkInterface) {
            return $this->_links;
        }

        return null;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->_status = $status;

        return $this;
    }

    /**
     * Get the HTTP status code applicable to this problem, expressed as a string value.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return !empty($this->_status) ? (string) $this->_status : null;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->_code = $code;

        return $this;
    }

    /**
     * Get an application-specific error code, expressed as a string value.
     *
     * @return string|null
     */
    public function getCode()
    {
        return !is_null($this->_code) ? (string) $this->_code : null;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->_title = $title;

        return $this;
    }

    /**
     * Get a short, human-readable summary of the problem.
     *
     * It should not change from occurrence to occurrence of the problem, except for purposes of localization.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return !empty($this->_title) ? (string) $this->_title : null;
    }

    /**
     * @param $detail
     * @return $this
     */
    public function setDetail($detail)
    {
        $this->_detail = $detail;

        return $this;
    }

    /**
     * Get a human-readable explanation specific to this occurrence of the problem.
     *
     * @return string|null
     */
    public function getDetail()
    {
        return !empty($this->_detail) ? (string) $this->_detail : null;
    }

    /**
     * @return SourceObject
     * @deprecated use `getSourceObject()`
     */
    public function source()
    {
        if (!$this->_source instanceof SourceObject) {
            $this->_source = new SourceObject();
        }

        return $this->_source;
    }

    /**
     * @param SourceObject|array|null $source
     * @return $this
     */
    public function setSource($source)
    {
        if (is_array($source)) {
            $source = (new SourceObject())->exchangeArray($source);
        } elseif (!is_null($source) && !$source instanceof SourceObjectInterface) {
            throw new \InvalidArgumentException('Expecting a SourceObject, array or null');
        }

        $this->_source = $source;

        return $this;
    }

    /**
     * @return SourceObject|null
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * @return SourceObjectInterface
     */
    public function getSourceObject()
    {
        if (!$this->_source instanceof SourceObjectInterface) {
            $this->_source = new SourceObject();
        }

        return $this->_source;
    }

    /**
     * @return bool
     */
    public function hasSource()
    {
        return $this->_source instanceof SourceObject;
    }

    /**
     * @param array|null $meta
     * @return $this
     */
    public function setMeta($meta)
    {
        if (!is_array($meta) && !is_null($meta)) {
            throw new \InvalidArgumentException('Expecting meta to be an array or null.');
        }

        $this->_meta = $meta;

        return $this;
    }

    /**
     * Get error meta information.
     *
     * @return array|null
     */
    public function getMeta()
    {
        return is_array($this->_meta) ? $this->_meta : null;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function exchangeArray(array $input)
    {
        if (array_key_exists(static::ID, $input)) {
            $this->setId($input[static::ID]);
        }

        if (array_key_exists(static::LINKS, $input)) {
            $this->setLinks($input[static::LINKS]);
        }

        if (array_key_exists(static::STATUS, $input)) {
            $this->setStatus($input[static::STATUS]);
        }

        if (array_key_exists(static::CODE, $input)) {
            $this->setCode($input[static::CODE]);
        }

        if (array_key_exists(static::TITLE, $input)) {
            $this->setTitle($input[static::TITLE]);
        }

        if (array_key_exists(static::DETAIL, $input)) {
            $this->setDetail($input[static::DETAIL]);
        }

        if (array_key_exists(static::SOURCE, $input)) {

            $source = $input[static::SOURCE];

            if (is_null($source) || is_array($source) || $source instanceof SourceObject) {
                $this->setSource($source);
            }
        }

        if (array_key_exists(static::META, $input)) {

            $meta = $input[static::META];

            if (is_null($meta) || is_array($meta)) {
                $this->setMeta($meta);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            static::ID => $this->getId(),
            static::LINKS => $this->getLinks(),
            static::STATUS => $this->getStatus(),
            static::CODE => $this->getCode(),
            static::TITLE => $this->getTitle(),
            static::DETAIL => $this->getDetail(),
            static::SOURCE => $this->getSource(),
            static::META => $this->getMeta(),
        ];
    }

    /**
     * @param array $input
     * @return static
     */
    public static function create(array $input)
    {
        $error = new static();
        $error->exchangeArray($input);
        return $error;
    }
}
