<?php

namespace CloudCreativity\JsonApi\Object;

class ObjectProxyTraitTest extends \PHPUnit_Framework_TestCase
{

    const KEY_A = 'foo';
    const VALUE_A = 'foobar';

    const KEY_B = 'bar';
    const VALUE_B = 'bazbat';

    const KEY_C = 'baz';

    protected $proxy;

    /**
     * @var ObjectProxyTrait
     */
    protected $trait;

    protected function setUp()
    {
        $this->proxy = new \stdClass();
        $this->proxy->{static::KEY_A} = static::VALUE_A;
        $this->proxy->{static::KEY_B} = static::VALUE_B;

        $this->trait = $this->getMockForTrait('CloudCreativity\JsonApi\Object\ObjectProxyTrait');
    }

    public function testSetProxy()
    {
        $this->assertSame($this->trait, $this->trait->setProxy($this->proxy));
        $this->assertSame($this->proxy, $this->trait->getProxy());
    }

    public function testGet()
    {
        $this->trait->setProxy($this->proxy);
        $this->assertSame(static::VALUE_A, $this->trait->get(static::KEY_A));
        $this->assertNull($this->trait->get(static::KEY_C));
        $this->assertFalse($this->trait->get(static::KEY_C, false));
    }

    public function testSet()
    {
        $this->assertSame($this->trait, $this->trait->set(static::KEY_A, static::VALUE_A));
        $this->assertSame(static::VALUE_A, $this->trait->get(static::KEY_A));
    }

    public function testHas()
    {
        $this->assertFalse($this->trait->has(static::KEY_A));
        $this->trait->set(static::KEY_A, static::VALUE_A);
        $this->assertTrue($this->trait->has(static::KEY_A));

        $this->trait->set(static::KEY_B, null);
        $this->assertTrue($this->trait->has(static::KEY_B));
    }

    public function testHasAll()
    {
        $this->trait->setProxy($this->proxy);

        $this->assertTrue($this->trait->hasAll([static::KEY_A, static::KEY_B]));
        $this->assertFalse($this->trait->hasAll([static::KEY_A, static::KEY_B, static::KEY_C]));
    }

    public function testHasAny()
    {
        $this->trait->setProxy($this->proxy);
        $this->assertTrue($this->trait->hasAny([static::KEY_A, static::KEY_C]));
        $this->assertFalse($this->trait->hasAny([static::KEY_C]));
    }

    public function testRemove()
    {
        $this->trait->set(static::KEY_A, static::VALUE_A);
        $this->assertSame($this->trait, $this->trait->remove(static::KEY_A));
        $this->assertNull($this->trait->get(static::KEY_A));
        $this->assertFalse($this->trait->has(static::KEY_A));
    }

    public function testGetProperties()
    {
        $this->trait->setProxy($this->proxy);
        $expected = (array) $this->proxy;
        $expected[static::KEY_C] = null;
        $keys = [static::KEY_A, static::KEY_B, static::KEY_C];

        $this->assertEquals($expected, $this->trait->getProperties($keys));
        $expected[static::KEY_C] = false;
        $this->assertEquals($expected, $this->trait->getProperties($keys, false));
    }

    public function testSetProperties()
    {
        $expected = (array) $this->proxy;

        $this->assertSame($this->trait, $this->trait->setProperties($expected));
        $this->assertEquals(static::VALUE_A, $this->trait->get(static::KEY_A));
        $this->assertEquals(static::VALUE_B, $this->trait->get(static::KEY_B));
    }

    public function testRemoveProperties()
    {
        $this->trait->setProxy($this->proxy);

        $this->assertSame($this->trait, $this->trait->removeProperties([static::KEY_A, static::KEY_C]));
        $this->assertFalse($this->trait->has(static::KEY_A));
    }

    public function testReduce()
    {
        $this->trait->setProxy($this->proxy);
        $expected = clone $this->proxy;
        unset($expected->{static::KEY_B});

        $this->assertSame($this->trait, $this->trait->reduce([static::KEY_A, static::KEY_C]));
        $this->assertEquals($expected, $this->trait->getProxy());
    }

    public function testKeys()
    {
        $this->assertEmpty($this->trait->keys());
        $this->trait->setProxy($this->proxy);
        $this->assertEquals([static::KEY_A, static::KEY_B], $this->trait->keys());
    }

    public function testArrayExchangeable()
    {
        $arr = [
            static::KEY_A => static::VALUE_A,
            static::KEY_B => static::VALUE_B,
        ];

        $this->assertSame($this->trait, $this->trait->exchangeArray($arr));
        $this->assertEquals($arr, $this->trait->toArray());
        $this->assertEquals($this->proxy, $this->trait->getProxy());
    }
}