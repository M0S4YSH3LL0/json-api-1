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

namespace CloudCreativity\JsonApi\Object\Resource;

use CloudCreativity\JsonApi\Object\StandardObject;
use CloudCreativity\JsonApi\Object\Relationships\Relationships;
use CloudCreativity\JsonApi\Object\ResourceIdentifier\ResourceIdentifier;
use CloudCreativity\JsonApi\TestCase;
use stdClass;

class ResourceTest extends TestCase
{

    const TYPE = 'foo';
    const ID = 123;

    protected $data;

    protected function setUp()
    {
        $data = new stdClass();
        $data->{Resource::TYPE} = static::TYPE;
        $data->{Resource::ID} = static::ID;
        $data->{Resource::ATTRIBUTES} = new stdClass();
        $data->{Resource::ATTRIBUTES}->foo = 'bar';
        $data->{Resource::RELATIONSHIPS} = new stdClass();
        $data->{Resource::RELATIONSHIPS}->baz = null;
        $data->{Resource::META} = new stdClass();
        $data->{Resource::META}->bat = 'foobar';

        $this->data = $data;
    }

    public function testGetType()
    {
        $object = new Resource($this->data);
        $this->assertSame(static::TYPE, $object->type());
    }

    public function testGetId()
    {
        $object = new Resource($this->data);
        $this->assertSame(static::ID, $object->id());
    }

    public function testHasId()
    {
        $object = new Resource($this->data);
        $this->assertTrue($object->hasId());
        unset($this->data->{Resource::ID});
        $this->assertFalse($object->hasId());
    }

    public function testGetIdentifier()
    {
        $expected = ResourceIdentifier::create(self::TYPE, self::ID);

        $object = new Resource($this->data);
        $this->assertEquals($expected, $object->identifier());
    }

    public function testGetAttributes()
    {
        $object = new Resource($this->data);
        $expected = new StandardObject($this->data->{Resource::ATTRIBUTES});

        $this->assertEquals($expected, $object->attributes());
    }

    public function testGetEmptyAttributes()
    {
        unset($this->data->{Resource::ATTRIBUTES});
        $object = new Resource($this->data);
        $this->assertEquals(new StandardObject(), $object->attributes());
    }

    public function testHasAttributes()
    {
        $object = new Resource($this->data);
        $this->assertTrue($object->hasAttributes());
        unset($this->data->{Resource::ATTRIBUTES});
        $this->assertFalse($object->hasAttributes());
    }

    public function testGetRelationships()
    {
        $expected = new Relationships($this->data->{Resource::RELATIONSHIPS});
        $object = new Resource($this->data);

        $this->assertEquals($expected, $object->relationships());
    }

    public function testHasRelationships()
    {
        $object = new Resource($this->data);
        $this->assertTrue($object->hasRelationships());
        unset($this->data->{Resource::RELATIONSHIPS});
        $this->assertFalse($object->hasRelationships());
    }

    public function getMeta()
    {
        $expected = new StandardObject($this->data->{Resource::META});
        $object = new Resource($this->data);
        $this->assertEquals($expected, $object->meta());
    }

    public function testHasMeta()
    {
        $object = new Resource($this->data);
        $this->assertTrue($object->hasMeta());
        unset($this->data->{Resource::META});
        $this->assertFalse($object->hasMeta());
    }
}
