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

namespace CloudCreativity\JsonApi\Object\ResourceIdentifier;

use CloudCreativity\JsonApi\Object\StandardObject;

class ResourceIdentifierTest extends \PHPUnit_Framework_TestCase
{

    const TYPE = 'foo';
    const ID = 123;

    public function testType()
    {
        $identifier = new ResourceIdentifier();

        $this->assertFalse($identifier->hasType());
        $this->assertSame($identifier, $identifier->setType(static::TYPE));
        $this->assertSame(static::TYPE, $identifier->getType());
        $this->assertTrue($identifier->hasType());

        return $identifier;
    }

    /**
     * @depends testType
     */
    public function testIsType(ResourceIdentifier $identifier)
    {
        $this->assertTrue($identifier->isType(static::TYPE));
        $this->assertFalse($identifier->isType('invalid-type'));
        $this->assertTrue($identifier->isType(['not-a-match', static::TYPE]));
    }

    /**
     * @depends testType
     */
    public function testId(ResourceIdentifier $identifier)
    {
        $this->assertFalse($identifier->hasId());
        $this->assertSame($identifier, $identifier->setId(static::ID));
        $this->assertSame(static::ID, $identifier->getId());
        $this->assertTrue($identifier->hasId());

        return $identifier;
    }

    public function testIsComplete()
    {
        $this->assertFalse((new ResourceIdentifier())->isComplete());
        $this->assertFalse((new ResourceIdentifier())->setType(static::TYPE)->isComplete());
        $this->assertFalse((new ResourceIdentifier())->setId(static::ID)->isComplete());

        $complete = new ResourceIdentifier();
        $complete->setType(static::TYPE)->setId(static::ID);

        $this->assertTrue($complete->isComplete());
    }

    public function testMapType()
    {
        $identifier = (new ResourceIdentifier())->setType(static::TYPE);
        $expected = 'My\Class';

        $map = [
            'not-a-match' => 'unexpected',
            static::TYPE => $expected,
        ];

        $this->assertSame($expected, $identifier->mapType($map));

        $this->setExpectedException('RuntimeException');
        $identifier->mapType([
            'not-a-match' => 'unexpected',
        ]);
    }

    public function testMeta()
    {
        $identifier = new ResourceIdentifier();

        $this->assertEquals(new StandardObject(), $identifier->getMeta());

        $meta = new \stdClass();
        $meta->foo = 'bar';
        $expected = new StandardObject($meta);

        $identifier->set(ResourceIdentifier::META, $meta);

        $this->assertEquals($expected, $identifier->getMeta());
    }
}
