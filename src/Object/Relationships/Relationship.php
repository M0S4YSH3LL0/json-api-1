<?php

namespace CloudCreativity\JsonApi\Object\Relationships;

use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifierCollection;
use CloudCreativity\JsonApi\Object\StandardObject;

class Relationship extends StandardObject
{

    const DATA = 'data';
    const META = 'meta';

    /**
     * @return ResourceIdentifier|ResourceIdentifierCollection|null
     */
    public function getData()
    {
        $data = $this->get(static::DATA);

        if (is_null($data)) {
            return null;
        } elseif (is_object($data)) {
            return new ResourceIdentifier($data);
        } elseif (is_array($data)) {
            return ResourceIdentifierCollection::create($data);
        }

        throw new \RuntimeException('Invalid data value on Relationship.');
    }

    /**
     * @return bool
     */
    public function isBelongsTo()
    {
        if (!$this->has(static::DATA)) {
            return false;
        }

        $data = $this->get(static::DATA);

        return is_null($data) || is_object($data);
    }

    /**
     * @return bool
     */
    public function isHasMany()
    {
        return is_array($this->get(static::DATA));
    }

    /**
     * @return StandardObject
     */
    public function getMeta()
    {
        return new StandardObject($this->get(static::META));
    }

    /**
     * @return bool
     */
    public function hasMeta()
    {
        return $this->has(static::META);
    }
}